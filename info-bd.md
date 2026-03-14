   commuting_sites {
       int id PK
       int promoter_id
       string name
       string slug
       int ratio_origin
       int ratio_origin_return
       int dropdown_origin
       int dropdown_destination
       string template
       string shape
       datetime deleted_at
   }


   config_options {
       int id PK
       string key
       string default_value
   }


   commuting_sites_config_values {
       int id PK
       int key_id FK
       int commuting_site_id FK
       string lang
       string value
   }


   sites {
       int id PK
       int vertical_id
       int commuting_site_id FK
       int company_event_id
       int country_id
       string name
   }


   routes {
       int id PK
       string title
       string invitation_code
       int province_id FK
       int primary_site FK
       int use_schedules
       int status
       string polyline_raw
       datetime start_timestamp
       datetime end_timestamp
   }


   commuting_sites_routes {
       int id PK
       int site_id FK
       int route_id FK
       int return
       int active
       date publish
       int primary
       datetime deleted_at
   }


   commuting_routes_data {
       int id PK
       int route_id FK
       int calendar_id
       int route_circular
       int vinculation_route
       int on_demand
       int shuttle
       int mon
       int tue
       int wed
       int thu
       int fri
       int sat
       int sun
       date date_init
       date date_finish
   }


   route_stops {
       int id PK
       int route_id FK
       int stop_type_id FK
       int type_stop
       float lat
       float lng
       string title
       string known_title
       int external_stop_id FK
       datetime start_timestamp
       datetime end_timestamp
   }


   stop_types {
       int id PK
   }


   stop_external {
       int id PK
       string name
   }


   routes_rates {
       int id PK
       int route_id FK
       int status
       int type_rate_id
       int period_rate_id
       date date_init_valid
       date date_finish_valid
   }


   rates_sites {
       int id PK
       int rates_id FK
       int sites_id FK
       int status
       datetime deleted_at
   }


   rates_routes {
       int id PK
       int rates_sites_id FK
       int routes_id FK
       decimal price
   }


   route_schedules {
       int id PK
       int route_id FK
       int track_id FK
       date start_date
       date end_date
       int monday
       int tuesday
       int wednesday
       int thursday
       int friday
       int saturday
       int sunday
       datetime deleted_at
   }


   tracks {
       int id PK
       int route_id FK
       string time
       string polyline_raw
       string public_name
       datetime deleted_at
   }


   track_stops {
       int id PK
       int track_id FK
       int route_stop_id FK
       int stop_type_id FK
       float lat
       float lng
       int seconds_until_arrival
       int seconds_until_departure
   }


   commuting_sites_destinations {
       int id PK
       int commuting_site_id FK
       string name
       float latitude
       float longitude
       int search
       int propose
   }


   commuting_companies {
       int id PK
       int site_id FK
       string name
       datetime deleted_at
   }


   commuting_companies_shift {
       int id PK
       int company_id FK
       string name
       datetime deleted_at
   }


   routes_shifts {
       int route_id FK
       int commuting_company_shift_id FK
   }


   provinces {
       int id PK
       string timezone
   }


   commuting_sites ||--o{ commuting_sites_config_values : "key_id" 
   config_options ||--o{ commuting_sites_config_values : "key_id"
   commuting_sites ||--o| sites : "id"
   commuting_sites ||--o{ commuting_sites_routes : "site_id"
   routes ||--o{ commuting_sites_routes : "route_id"
   routes ||--o| commuting_routes_data : "route_id"
   routes }o--o| provinces : "province_id"
   routes }o--o| sites : "primary_site"
   routes ||--o{ route_stops : "route_id"
   route_stops }o--o| stop_types : "stop_type_id"
   route_stops }o--o| stop_external : "external_stop_id"
   routes ||--o{ routes_rates : "route_id"
   commuting_sites ||--o{ rates_sites : "sites_id"
   rates_sites ||--o{ rates_routes : "rates_sites_id"
   routes ||--o{ rates_routes : "routes_id"
   routes ||--o{ route_schedules : "route_id"
   tracks ||--o{ route_schedules : "track_id"
   routes ||--o{ tracks : "route_id"
   tracks ||--o{ track_stops : "track_id"
   route_stops ||--o{ track_stops : "route_stop_id"
   commuting_sites ||--o{ commuting_sites_destinations : "commuting_site_id"
   commuting_sites ||--o{ commuting_companies : "site_id"
   commuting_companies ||--o{ commuting_companies_shift : "company_id"
   routes ||--o{ routes_shifts : "route_id"
   commuting_companies_shift ||--o{ routes_shifts : "commuting_company_shift_id"
