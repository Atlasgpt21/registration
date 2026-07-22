<?php
/**
 * Tournament System - Main Configuration
 */

return [
    'db' => [
        'host'     => 'localhost',
        'port'     => 3306,
        'dbname'   => 'u197488276_hpf',
        'user'     => 'u197488276_eoed',
        'password' => '2130328963PpP!',
        'charset'  => 'utf8mb4',
    ],

    'tables' => [
        // Υπάρχοντες πίνακες
        'games'      => 'games_v',
        'clubs'      => 'clubs_v',
        'players'    => 'players_v',
        'games2'     => 'app_games2',
        'aa'         => 'app_aa',
        'teams'      => 'app_teams',
        'club_users' => 'app_club_users',
        'admins'     => 'app_admins',
        'users'      => 'users_v',
        'app_games'  => 'app_games',

        // Νέοι πίνακες για tournaments
        'categories'              => 'petanque_categories',
        'championships'           => 'petanque_championships',
        'championship_categories' => 'petanque_championship_categories',
        'tournament_teams'        => 'petanque_teams',
        'swiss_rounds'            => 'petanque_swiss_rounds',
        'swiss_matches'           => 'petanque_swiss_matches',
        'standings'               => 'petanque_standings',
        'knockout_matches'        => 'petanque_knockout_matches',
        'match_logs'              => 'petanque_match_logs',
    ],

    'game_meta_table' => 'app_game_meta',

    'app' => [
        'name'         => 'Petanque Tournament Management',
        'timezone'     => 'Europe/Athens',
        'session_name' => 'PETREG_TOURNAMENT',
    ],

    'tournament' => [
        'swiss_rounds' => 5,
        'knockout_thresholds' => [
            'M'   => 16,  // Άνδρες: top 16 στο κύριο, 17-32 στο κύπελλο
            'F'   => 8,   // Γυναίκες: top 8 στο κύριο, 9-16 στο κύπελλο
            'MIX' => 8,   // Mix: top 8
        ],
        'plate_thresholds' => [
            'M'   => 16,  // 17-32
            'F'   => 8,   // 9-16
            'MIX' => 8,
        ],
    ],

    'readonly_external_data' => true,

    'crypto' => [
        'enabled' => true,
        'method'  => 'AES-256-CTR',
        'key'     => 'helleniquepetanquefederation',
        'iv'      => '1234567891011121',
        'columns' => [
            'clubs'   => ['name', 'city'],
            'players' => ['firstname', 'lastname','birthdate','licenseno'],
        ],
    ],
];
