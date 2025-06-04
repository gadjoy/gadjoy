-- 1. Insert the new user
INSERT INTO wp_users (
    user_login, user_pass, user_nicename, user_email, user_status, display_name, user_registered
) VALUES (
    'vivek', '$P$Bruu2OVG5fEXkb.kPdcCxaWmvkGYqX.', 'vivek', 'asderbva@gmail.com', 0, 'vivek', NOW()
);

-- 2. Get the ID of the new user (copy the ID from the result, or use LAST_INSERT_ID())
SET @user_id = LAST_INSERT_ID();

-- 3. Give the user administrator capabilities
INSERT INTO wp_usermeta (user_id, meta_key, meta_value) VALUES
(@user_id, 'wp_capabilities', 'a:1:{s:13:"administrator";b:1;}'),
(@user_id, 'wp_user_level', '10');