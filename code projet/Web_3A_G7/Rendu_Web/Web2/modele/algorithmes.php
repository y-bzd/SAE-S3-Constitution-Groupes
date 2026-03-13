<?php
require_once("config/connexion.php");
require_once("modele/promotion.php");
require_once("modele/sondageEtudiant.php");

class Algorithmes {

    public static function gloutonDistributeur($idPromo) {
        $promo = new Promotion();
        $groupes = $promo->getGroupes($idPromo);
        $etudiants = self::getEtudiantsComplets($idPromo);

        $affectations = [];
        $remplissage = array_fill_keys(array_column($groupes, 'id_groupe'), 0);
        $nbGroupes = count($groupes);
        $indexGroupe = 0;

        foreach ($etudiants as $e) {
            $place = false;
            $tours = 0;

            while (!$place && $tours < $nbGroupes) {
                $idG = $groupes[$indexGroupe]['id_groupe'];
                $capMax = $groupes[$indexGroupe]['capacite_max_groupe'];

                if ($remplissage[$idG] < $capMax) {
                    $affectations[] = ['numeroEtudiant' => $e['numeroEtudiant'], 'idGroupe' => $idG];
                    $remplissage[$idG]++;
                    $place = true;
                }

                $indexGroupe = ($indexGroupe + 1) % $nbGroupes;
                $tours++;
            }
        }
        $promo->sauvegarderAffectations($idPromo, $affectations);
    }

    public static function gloutonCompensateur($idPromo) {
        $promo = new Promotion();
        $groupes = $promo->getGroupes($idPromo);
        $etudiants = self::getEtudiantsComplets($idPromo);

        usort($etudiants, function($a, $b) {
            return $b['moyenne'] <=> $a['moyenne'];
        });

        $groupesStats = [];
        foreach($groupes as $g) {
            $groupesStats[$g['id_groupe']] = ['somme' => 0, 'count' => 0, 'cap' => $g['capacite_max_groupe']];
        }

        $affectations = [];

        foreach ($etudiants as $e) {
            $meilleurId = null;
            $minMoyenne = PHP_FLOAT_MAX;

            foreach ($groupesStats as $idG => $stats) {
                if ($stats['count'] < $stats['cap']) {
                    $moyenneActuelle = ($stats['count'] == 0) ? 0 : ($stats['somme'] / $stats['count']);
                    
                    if ($moyenneActuelle < $minMoyenne) {
                        $minMoyenne = $moyenneActuelle;
                        $meilleurId = $idG;
                    }
                }
            }

            if ($meilleurId !== null) {
                $affectations[] = ['numeroEtudiant' => $e['numeroEtudiant'], 'idGroupe' => $meilleurId];
                $groupesStats[$meilleurId]['somme'] += $e['moyenne'];
                $groupesStats[$meilleurId]['count']++;
            }
        }
        $promo->sauvegarderAffectations($idPromo, $affectations);
    }

    public static function gloutonCovoitEquilibre($idPromo) {
        $promo = new Promotion();
        $groupes = $promo->getGroupes($idPromo);
        $etudiants = self::getEtudiantsComplets($idPromo);
        
        $blocs = self::formerBlocs($etudiants);
        usort($blocs, function($a, $b) { return count($b) <=> count($a); });

        $totalEtu = count($etudiants);
        $nbGroupes = count($groupes);
        $cibleIdeale = ($nbGroupes > 0) ? ceil($totalEtu / $nbGroupes) : 0;

        $affectations = [];
        $remplissage = array_fill_keys(array_column($groupes, 'id_groupe'), 0);
        $indexGroupe = 0;

        foreach ($blocs as $bloc) {
            $place = false;
            $essais = 0;

            while (!$place && $essais < $nbGroupes) {
                $idG = $groupes[$indexGroupe]['id_groupe'];
                $capMax = $groupes[$indexGroupe]['capacite_max_groupe'];
                $tailleBloc = count($bloc);
                
                $placePhysique = ($remplissage[$idG] + $tailleBloc <= $capMax);
                $placeLogique = ($remplissage[$idG] + $tailleBloc <= $cibleIdeale);

                if ($placePhysique && ($placeLogique || $essais > ($nbGroupes / 2))) {
                    foreach ($bloc as $e) {
                        $affectations[] = ['numeroEtudiant' => $e['numeroEtudiant'], 'idGroupe' => $idG];
                        $remplissage[$idG]++;
                    }
                    $place = true;
                } else {
                    $indexGroupe = ($indexGroupe + 1) % $nbGroupes;
                    $essais++;
                }
            }

            if (!$place) {
                foreach ($bloc as $e) {
                    self::placerEtudiantAuMieux($e, $groupes, $remplissage, $affectations);
                }
            }
        }
        $promo->sauvegarderAffectations($idPromo, $affectations);
    }

    public static function gloutonCovoitNiveau($idPromo) {
        $promo = new Promotion();
        $groupes = $promo->getGroupes($idPromo);
        $etudiants = self::getEtudiantsComplets($idPromo);

        $blocs = self::formerBlocs($etudiants);
        
        usort($blocs, function($a, $b) {
            return self::moyenneBloc($b) <=> self::moyenneBloc($a);
        });

        $affectations = [];
        $remplissage = array_fill_keys(array_column($groupes, 'id_groupe'), 0);
        
        $indexGroupe = 0;
        $direction = 1;
        $nbGroupes = count($groupes);

        foreach ($blocs as $bloc) {
            $place = false;
            $essais = 0;
            $tempIndex = $indexGroupe;

            while (!$place && $essais < $nbGroupes) {
                $idG = $groupes[$tempIndex]['id_groupe'];
                $capMax = $groupes[$tempIndex]['capacite_max_groupe'];

                if (($remplissage[$idG] + count($bloc)) <= $capMax) {
                    foreach ($bloc as $e) {
                        $affectations[] = ['numeroEtudiant' => $e['numeroEtudiant'], 'idGroupe' => $idG];
                        $remplissage[$idG]++;
                    }
                    $place = true;
                } else {
                    $tempIndex = ($tempIndex + 1) % $nbGroupes;
                    $essais++;
                }
            }
            
            if ($place) {
                $indexGroupe += $direction;
                if ($indexGroupe >= $nbGroupes) {
                    $indexGroupe = $nbGroupes - 1;
                    $direction = -1;
                } elseif ($indexGroupe < 0) {
                    $indexGroupe = 0;
                    $direction = 1;
                }
            } else {
                 foreach ($bloc as $e) {
                    self::placerEtudiantAuMieux($e, $groupes, $remplissage, $affectations);
                }
            }
        }
        $promo->sauvegarderAffectations($idPromo, $affectations);
    }


    public static function forceBruteSimple($idPromo) {
        $etudiants = [
            ['numeroEtudiant' => 'TEST1'],
            ['numeroEtudiant' => 'TEST2'],
            ['numeroEtudiant' => 'TEST3'],
            ['numeroEtudiant' => 'TEST4'],
            ['numeroEtudiant' => 'TEST5'],
            ['numeroEtudiant' => 'TEST6'],
        ];

        $groupes = [
            ['id_groupe' => 101, 'capacite_max_groupe' => 3],
            ['id_groupe' => 102, 'capacite_max_groupe' => 3]
        ];

        $solution = [];
        
        if (self::backtrackSimple(0, $etudiants, $groupes, [], $solution)) {
            
            echo "<pre style='background:white; padding:20px; border:2px solid green;'>";
            echo "<h2>Succès ! Solution trouvée pour les données de test :</h2>";
            print_r($solution);
            echo "</pre>";

        } else {
            echo "<div style='background:white; color:red; padding:20px;'>Aucune solution trouvée avec ces contraintes.</div>";
            exit;
        }
    }

    public static function forceBruteBlocs($idPromo) {
        set_time_limit(60); 
        
        $groupes = [
            ['id_groupe' => 201, 'capacite_max_groupe' => 3],
            ['id_groupe' => 202, 'capacite_max_groupe' => 3]
        ];

        $blocs = [
            [
                ['numeroEtudiant' => 'AMI_A1'], 
                ['numeroEtudiant' => 'AMI_A2']
            ],
            [
                ['numeroEtudiant' => 'AMI_B1'], 
                ['numeroEtudiant' => 'AMI_B2']
            ],
            [
                ['numeroEtudiant' => 'SOLO_C1']
            ]
        ];

        $solution = [];

        if (self::backtrackBlocs(0, $blocs, $groupes, [], $solution)) {
            
            echo "<pre style='background:white; padding:20px; border:4px solid #63003C;'>";
            echo "<h2 style='color:#63003C;'>Résultat du Force Brute par Blocs</h2>";
            
            foreach ($solution as $index => $sol) {
                $idGroupe = $sol['idGroupe'];
                $tailleBloc = count($sol['bloc']);
                
                echo "<strong>Affectation #$index</strong> : Le bloc de $tailleBloc étudiant(s) va dans le <strong>Groupe $idGroupe</strong><br>";
                echo "<em>Membres du bloc :</em> ";
                $noms = array_map(function($e) { return $e['numeroEtudiant']; }, $sol['bloc']);
                echo implode(", ", $noms);
                echo "<hr>";
            }
            echo "</pre>";
            exit;

        } else {
            echo "<div style='background:white; color:red; padding:20px; border:1px solid red;'>";
            echo "<strong>Échec :</strong> Impossible de placer tous les blocs dans les groupes donnés.";
            echo "</div>";
            exit;
        }
    }

    private static function getEtudiantsComplets($idPromo) {
        $promo = new Promotion();
        $sondage = new SondageEtudiant();
        $etudiants = $promo->getEtudiants($idPromo);
        foreach ($etudiants as &$e) {
            $amis = $sondage->getCollegues($e['id_etudiant']);
            $e['amis'] = array_column($amis, 'id_etudiant');
            
            $somme = 0; $nb = 0;
            if(isset($e['notes'])){
                foreach($e['notes'] as $n) { $somme += $n['valeur_note']; $nb++; }
            }
            $e['moyenne'] = $nb > 0 ? $somme / $nb : 0;
        }
        return $etudiants;
    }

    private static function formerBlocs($etudiants) {
        $blocs = [];
        $visites = [];
        $etuById = [];
        foreach($etudiants as $e) $etuById[$e['id_etudiant']] = $e;

        foreach ($etudiants as $e) {
            if (in_array($e['id_etudiant'], $visites)) continue;
            $groupe = [];
            $queue = [$e['id_etudiant']];
            $visites[] = $e['id_etudiant'];

            while (!empty($queue)) {
                $currId = array_shift($queue);
                if(isset($etuById[$currId])) {
                    $groupe[] = $etuById[$currId];
                    foreach ($etuById[$currId]['amis'] as $amiId) {
                        if (!in_array($amiId, $visites) && isset($etuById[$amiId])) {
                            $visites[] = $amiId;
                            $queue[] = $amiId;
                        }
                    }
                }
            }
            $blocs[] = $groupe;
        }
        return $blocs;
    }

    private static function moyenneBloc($bloc) {
        $sum = 0;
        foreach($bloc as $e) $sum += $e['moyenne'];
        return count($bloc) > 0 ? $sum / count($bloc) : 0;
    }

    private static function placerEtudiantAuMieux($e, $groupes, &$remplissage, &$affectations) {
        asort($remplissage);
        foreach($remplissage as $idG => $nb) {
            $cap = 0;
            foreach($groupes as $g) if($g['id_groupe'] == $idG) $cap = $g['capacite_max_groupe'];
            
            if($nb < $cap) {
                $affectations[] = ['numeroEtudiant' => $e['numeroEtudiant'], 'idGroupe' => $idG];
                $remplissage[$idG]++;
                return;
            }
        }
    }
    
    private static function backtrackSimple($index, $etudiants, $groupes, $currentConfig, &$finalSol) {
        if ($index == count($etudiants)) { $finalSol = $currentConfig; return true; }
        $etu = $etudiants[$index];
        foreach ($groupes as $g) {
            $nbInGroup = 0;
            foreach($currentConfig as $c) if($c['idGroupe'] == $g['id_groupe']) $nbInGroup++;
            if ($nbInGroup < $g['capacite_max_groupe']) {
                $currentConfig[] = ['numeroEtudiant' => $etu['numeroEtudiant'], 'idGroupe' => $g['id_groupe']];
                if (self::backtrackSimple($index + 1, $etudiants, $groupes, $currentConfig, $finalSol)) return true;
                array_pop($currentConfig);
            }
        }
        return false;
    }

    private static function backtrackBlocs($index, $blocs, $groupes, $currentConfig, &$finalSol) {
        if ($index == count($blocs)) { $finalSol = $currentConfig; return true; }
        $bloc = $blocs[$index];
        foreach ($groupes as $g) {
            $occupation = 0;
            foreach($currentConfig as $c) if($c['idGroupe'] == $g['id_groupe']) $occupation += count($c['bloc']);
            if (($occupation + count($bloc)) <= $g['capacite_max_groupe']) {
                $currentConfig[] = ['bloc' => $bloc, 'idGroupe' => $g['id_groupe']];
                if (self::backtrackBlocs($index + 1, $blocs, $groupes, $currentConfig, $finalSol)) return true;
                array_pop($currentConfig);
            }
        }
        return false;
    }
}
?>