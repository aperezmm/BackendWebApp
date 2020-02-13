-- Create a new database called 'prueba'
-- Connect to the 'master' database to run this snippet
USE master
GO
-- Create the new database if it does not exist already
IF NOT EXISTS (
    SELECT name
        FROM sys.databases
        WHERE name = N'prueba'
)
CREATE DATABASE prueba
GO
-- Create a new table called 'productos' in schema 'SchemaName'
-- Drop the table if it already exists
IF OBJECT_ID('SchemaName.productos', 'U') IS NOT NULL
DROP TABLE SchemaName.productos
GO
-- Create the table in the specified schema
CREATE TABLE SchemaName.productos
(
    productosId INT NOT NULL PRIMARY KEY AUTO_INCREMENT, -- primary key column
    nombre [NVARCHAR](255),
    descripcion [NTEXT],
    precio [NVARCHAR](255),
    imagen [NVARCHAR](255)
    -- specify more columns here
)ENGINE=InnoDb;
GO
