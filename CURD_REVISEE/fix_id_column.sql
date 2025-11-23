-- Fix script to add id column to users table
-- Run this in phpMyAdmin if the PHP script doesn't work

-- Check if id column exists first (run this to see current structure)
-- DESCRIBE users;

-- Option 1: If table has NO data, run this:
ALTER TABLE `users` 
ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, 
ADD PRIMARY KEY (`id`);

-- Option 2: If table HAS data, run these commands in order:
-- Step 1: Add id column as nullable first
-- ALTER TABLE `users` ADD COLUMN `id` INT(11) NULL FIRST;

-- Step 2: Set sequential IDs for existing rows
-- SET @row_number = 0;
-- UPDATE `users` SET `id` = (@row_number:=@row_number + 1) WHERE `id` IS NULL;

-- Step 3: Make id NOT NULL, AUTO_INCREMENT, and PRIMARY KEY
-- ALTER TABLE `users` MODIFY COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

-- Verify the fix:
-- DESCRIBE users;
-- SHOW KEYS FROM users WHERE Key_name = 'PRIMARY';

