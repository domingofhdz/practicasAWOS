-- Adminer 5.4.1 MySQL 8.0.41 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DELIMITER ;;

DROP PROCEDURE IF EXISTS `agregarProducto`;;
CREATE PROCEDURE `agregarProducto` (IN `pNombre` text, IN `pIdCategoria` int, OUT `nuevoIdProducto` int, OUT `nuevoNombre` text, OUT `nuevoIdCategoria` int)
BEGIN
    INSERT INTO productos (nombre, idCategoria) VALUES (pNombre, pIdCategoria);
    SELECT idProducto, nombre, idCategoria
    INTO nuevoIdProducto, nuevoNombre, nuevoIdCategoria
    FROM productos
    ORDER BY idProducto DESC
    LIMIT 1;
END;;

DROP PROCEDURE IF EXISTS `eliminarProducto`;;
CREATE PROCEDURE `eliminarProducto` (IN `pIdProducto` int)
BEGIN
     DELETE FROM detalles_ventas
     WHERE idProducto = pIdProducto;
     DELETE FROM productos
     WHERE idProducto = pIdProducto;
END;;

DROP PROCEDURE IF EXISTS `modificarProducto`;;
CREATE PROCEDURE `modificarProducto` (IN `pIdProducto` int, IN `pNombre` text, IN `pIdCategoria` int)
BEGIN
     UPDATE productos
     SET nombre = pNombre,
     idCategoria = pIdCategoria
     WHERE idProducto = pIdProducto;
END;;

DELIMITER ;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `idCategoria` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `categorias` (`idCategoria`, `nombre`) VALUES
(1,	'Galletas'),
(2,	'Refrescos'),
(3,	'Fritos'),
(4,	'Dulces');

DROP TABLE IF EXISTS `detalles_ventas`;
CREATE TABLE `detalles_ventas` (
  `idDetalleVenta` int NOT NULL AUTO_INCREMENT,
  `idVenta` int NOT NULL,
  `idProducto` int NOT NULL,
  `precio` double NOT NULL,
  `cantidad` int NOT NULL,
  PRIMARY KEY (`idDetalleVenta`),
  KEY `idVenta` (`idVenta`),
  KEY `idProducto` (`idProducto`),
  CONSTRAINT `detalles_ventas_ibfk_1` FOREIGN KEY (`idVenta`) REFERENCES `ventas` (`idVenta`),
  CONSTRAINT `detalles_ventas_ibfk_2` FOREIGN KEY (`idProducto`) REFERENCES `productos` (`idProducto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `detalles_ventas` (`idDetalleVenta`, `idVenta`, `idProducto`, `precio`, `cantidad`) VALUES
(1,	1,	1,	20,	2),
(2,	1,	2,	21,	2),
(3,	2,	1,	20,	3);

DROP TABLE IF EXISTS `empleados`;
CREATE TABLE `empleados` (
  `idEmpleado` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`idEmpleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `empleados` (`idEmpleado`, `nombre`) VALUES
(1,	'Pepe'),
(2,	'Jose'),
(3,	'Zoe');

DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `idProducto` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `idCategoria` int DEFAULT NULL,
  PRIMARY KEY (`idProducto`),
  KEY `idCategoria` (`idCategoria`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`idCategoria`) REFERENCES `categorias` (`idCategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `productos` (`idProducto`, `nombre`, `idCategoria`) VALUES
(1,	'Sponch',	1),
(2,	'Coca Cola',	2),
(3,	'Doritos',	3),
(5,	'test 2b',	1),
(7,	'test 3',	NULL),
(8,	'test 4',	NULL),
(9,	'test 5',	NULL),
(10,	'test 6',	4),
(11,	'test 7',	3),
(12,	'test 8',	1),
(13,	'test 9',	4),
(14,	'test 10',	2);

DROP TABLE IF EXISTS `reportes`;
CREATE TABLE `reportes` (
  `idReporte` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) NOT NULL,
  `latitud` double DEFAULT NULL,
  `longitud` double DEFAULT NULL,
  `ubicacion` text,
  PRIMARY KEY (`idReporte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `reportes` (`idReporte`, `descripcion`, `latitud`, `longitud`, `ubicacion`) VALUES
(1,	'Prueba 1.',	28.722421666666662,	-100.53549166666666,	NULL),
(2,	'excelente ubicación, gracias',	28.722456237667366,	-100.53546406082626,	NULL),
(4,	'JUAN ANTONIO LARA ZAMARRIPIA 24005241',	28.722456237667366,	-100.53546406082626,	NULL),
(5,	'JUAN ANTONIO LARA ZAMARRIPIA 24005241',	28.722456237667366,	-100.53546406082626,	NULL),
(6,	'JUAN ANTONIO LARA ZAMARRIPIA 24005241',	28.722456237667366,	-100.53546406082626,	NULL),
(7,	'Prueba 2.',	28.56972795544903,	-100.61623836416899,	NULL),
(8,	'Prueba 3.',	19.360084999999998,	-99.05252273906652,	NULL);

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `idUsuario` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(20) NOT NULL,
  `contrasena` varchar(20) NOT NULL,
  `tipo` int NOT NULL,
  PRIMARY KEY (`idUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `usuarios` (`idUsuario`, `usuario`, `contrasena`, `tipo`) VALUES
(1,	'pepe',	'pepe',	1),
(2,	'juan',	'juan',	2);

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas` (
  `idVenta` int NOT NULL AUTO_INCREMENT,
  `idEmpleado` int NOT NULL,
  `fechaHora` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idVenta`),
  KEY `idEmpleado` (`idEmpleado`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`idEmpleado`) REFERENCES `empleados` (`idEmpleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `ventas` (`idVenta`, `idEmpleado`, `fechaHora`) VALUES
(1,	1,	'2025-12-30 17:00:00'),
(2,	1,	'2025-12-30 17:00:00'),
(3,	2,	'2026-01-12 12:09:02'),
(4,	3,	'2026-01-15 12:30:28');

DROP VIEW IF EXISTS `view_productos_categorias`;
CREATE TABLE `view_productos_categorias` (`idProducto` int, `nombre` varchar(50), `idCategoria` int, `categoria` varchar(50));


DROP VIEW IF EXISTS `view_productos_categorias_modificable`;
CREATE TABLE `view_productos_categorias_modificable` (`idProducto` int, `nombre` varchar(50), `IFNULL(idCategoria, 'Sin clasificar')` varchar(14));


DROP TABLE IF EXISTS `view_productos_categorias`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_productos_categorias` AS select `productos`.`idProducto` AS `idProducto`,`productos`.`nombre` AS `nombre`,`productos`.`idCategoria` AS `idCategoria`,ifnull(`categorias`.`nombre`,'Sin Categoría') AS `categoria` from (`productos` left join `categorias` on((`productos`.`idCategoria` = `categorias`.`idCategoria`)));

DROP TABLE IF EXISTS `view_productos_categorias_modificable`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_productos_categorias_modificable` AS select `productos`.`idProducto` AS `idProducto`,`productos`.`nombre` AS `nombre`,ifnull(`productos`.`idCategoria`,'Sin clasificar') AS `IFNULL(idCategoria, 'Sin clasificar')` from `productos`;

-- 2026-03-08 22:54:55 UTC
