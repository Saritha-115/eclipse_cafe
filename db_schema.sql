-- Eclipse Café Database Schema
-- Import this file into phpMyAdmin to create the database structure

-- Create database
CREATE DATABASE IF NOT EXISTS eclipse_cafe;
USE eclipse_cafe;

-- Table: menu_items (stores café menu items)
CREATE TABLE menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_available (is_available)
);

-- Table: orders (stores customer orders)
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    items TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

-- Table: admins (stores admin user credentials)
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username)
);

-- Table: contact_messages (optional - stores contact form submissions)
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_read (is_read)
);

-- Insert default admin user
-- Username: admin
-- Password: admin123 (CHANGE THIS AFTER FIRST LOGIN!)
INSERT INTO admins (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@eclipsecafe.com');

-- Insert sample menu items
INSERT INTO menu_items (name, description, price, category, image) VALUES 
('Espresso', 'Rich and bold Italian espresso shot', 3.50, 'Coffee', 'espresso.jpg'),
('Cappuccino', 'Espresso with steamed milk and foam', 4.50, 'Coffee', 'cappuccino.jpg'),
('Caffe Latte', 'Smooth espresso with steamed milk', 4.75, 'Coffee', 'latte.jpg'),
('Americano', 'Espresso with hot water', 3.75, 'Coffee', 'americano.jpg'),
('Mocha', 'Espresso with chocolate and steamed milk', 5.25, 'Coffee', 'mocha.jpg'),
('Iced Coffee', 'Cold brew coffee served over ice', 4.00, 'Cold Drinks', 'iced-coffee.jpg'),
('Frappe', 'Blended iced coffee with whipped cream', 5.50, 'Cold Drinks', 'frappe.jpg'),
('Green Tea Latte', 'Matcha green tea with steamed milk', 4.75, 'Tea', 'matcha.jpg'),
('Chai Latte', 'Spiced tea with steamed milk', 4.50, 'Tea', 'chai.jpg'),
('Croissant', 'Buttery French pastry', 3.25, 'Pastries', 'croissant.jpg'),
('Chocolate Muffin', 'Rich chocolate chip muffin', 3.50, 'Pastries', 'muffin.jpg'),
('Blueberry Cheesecake', 'Creamy cheesecake with blueberry topping', 6.50, 'Desserts', 'cheesecake.jpg'),
('Tiramisu', 'Classic Italian coffee-flavored dessert', 6.75, 'Desserts', 'tiramisu.jpg'),
('Club Sandwich', 'Triple-decker with turkey, bacon, and veggies', 8.50, 'Food', 'sandwich.jpg'),
('Caesar Salad', 'Fresh romaine with Caesar dressing and croutons', 7.50, 'Food', 'salad.jpg');

-- Insert sample orders for testing
INSERT INTO orders (customer_name, phone, email, items, total, status) VALUES 
('John Smith', '1234567890', 'john@email.com', 'Cappuccino x2, Croissant x1', 13.25, 'completed'),
('Sarah Johnson', '9876543210', 'sarah@email.com', 'Mocha x1, Blueberry Cheesecake x1', 11.75, 'pending'),
('Mike Davis', '5551234567', 'mike@email.com', 'Club Sandwich x1, Iced Coffee x1', 12.50, 'completed');

-- View to get order statistics (useful for admin dashboard)
CREATE VIEW order_stats AS
SELECT 
    COUNT(*) as total_orders,
    SUM(total) as total_revenue,
    AVG(total) as average_order,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders
FROM orders;

-- Trigger to update menu_items timestamp on update
DELIMITER //
CREATE TRIGGER before_menu_item_update 
BEFORE UPDATE ON menu_items
FOR EACH ROW 
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END;//
DELIMITER ;

-- Success message
SELECT 'Database setup completed successfully!' as message;