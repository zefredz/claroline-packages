CREATE TABLE IF NOT EXISTS `__CL_MAIN__epc_user_data` (
    user_id INT 11 NOT NULL,
    noma VARCHAR(12) NOT NULL,
    acad_year VARCHAR(12) NOT NULL,
    program_code VARCHAR(12) NOT NULL,
    otherData TEXT DEFAULT '',
    last_sync DATETIME DEFAULT '0000-00-00 00:00:00',
    
);

CREATE TABLE IF NOT EXISTS `__CL_MAIN__epc_query_cache` (
    query_id INT 11 NOT NULL,
    query_string VARCHAR(255) NOT NULL,
    query_date DATETIME DEFAULT '0000-00-00 00:00:00',
    query_status VARCHAR(8) NOT NULL,
    users_synced_list TEXT DEFAULT '',
    users_synced_cnt INT(11) NOT NULL,
    users_added_list TEXT DEFAULT '',
    users_added_cnt INT(11) NOT NULL,
    users_removed_list TEXT DEFAULT '',
    users_removed_cnt INT(11) NOT NULL,
    class_name VARCHAR(100) NOT NULL,

);