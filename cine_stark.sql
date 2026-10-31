-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 31-10-2026 a las 18:37:07
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cine_stark`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boletos`
--

DROP TABLE IF EXISTS `boletos`;
CREATE TABLE IF NOT EXISTS `boletos` (
  `id_boleto` int NOT NULL AUTO_INCREMENT,
  `id_funcion` int NOT NULL,
  `id_venta` int NOT NULL,
  `fila_columna` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_boleto`),
  KEY `id_funcion` (`id_funcion`),
  KEY `id_venta` (`id_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `boletos`
--

INSERT INTO `boletos` (`id_boleto`, `id_funcion`, `id_venta`, `fila_columna`) VALUES
(1, 3, 1, 'A6'),
(2, 3, 1, 'A7'),
(3, 3, 1, 'B8'),
(4, 3, 1, 'C4'),
(5, 3, 1, 'C5'),
(6, 3, 1, 'D2'),
(7, 1, 2, 'I10'),
(8, 1, 2, 'I11'),
(9, 1, 2, 'I9'),
(10, 1, 2, 'J10'),
(11, 1, 2, 'J11'),
(12, 1, 2, 'J9'),
(13, 3, 3, 'G4'),
(14, 3, 3, 'G5'),
(15, 3, 3, 'G6'),
(16, 3, 3, 'G7'),
(17, 3, 3, 'G8'),
(18, 3, 4, 'I10'),
(19, 3, 4, 'I5'),
(20, 3, 4, 'I6'),
(21, 3, 4, 'I7'),
(22, 3, 4, 'I8'),
(23, 3, 4, 'I9'),
(24, 9, 5, 'A1'),
(25, 9, 5, 'A10'),
(26, 9, 5, 'A11'),
(27, 9, 5, 'A2'),
(28, 9, 5, 'A3'),
(29, 9, 5, 'A4'),
(30, 9, 5, 'A5'),
(31, 9, 5, 'A6'),
(32, 9, 5, 'A7'),
(33, 9, 5, 'A8'),
(34, 9, 5, 'A9'),
(35, 9, 5, 'B1'),
(36, 9, 5, 'B10'),
(37, 9, 5, 'B11'),
(38, 9, 5, 'B2'),
(39, 9, 5, 'B3'),
(40, 9, 5, 'B4'),
(41, 9, 5, 'B5'),
(42, 9, 5, 'B6'),
(43, 9, 5, 'B7'),
(44, 9, 5, 'B8'),
(45, 9, 5, 'B9'),
(46, 9, 6, 'E5'),
(47, 9, 6, 'E6'),
(48, 3, 7, 'E5'),
(49, 3, 7, 'E6'),
(50, 3, 7, 'E7'),
(51, 3, 7, 'E8'),
(52, 8, 8, 'E5'),
(53, 8, 8, 'E6'),
(54, 5, 9, 'A11'),
(55, 5, 9, 'C11'),
(56, 5, 9, 'F4'),
(57, 6, 10, 'D5'),
(58, 6, 10, 'D6'),
(59, 3, 11, 'D10'),
(60, 3, 11, 'D11'),
(61, 3, 11, 'E10'),
(62, 3, 11, 'E11'),
(64, 14, 13, 'D5'),
(65, 14, 13, 'D6'),
(66, 14, 13, 'D7'),
(67, 14, 14, 'H2'),
(68, 14, 14, 'H3'),
(69, 14, 14, 'H4'),
(70, 14, 14, 'I2'),
(71, 14, 14, 'I3'),
(72, 14, 14, 'I4'),
(73, 21, 17, 'D4'),
(74, 21, 17, 'D6'),
(75, 21, 17, 'F6'),
(76, 21, 17, 'F7');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cortes_caja`
--

DROP TABLE IF EXISTS `cortes_caja`;
CREATE TABLE IF NOT EXISTS `cortes_caja` (
  `id_corte` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_inicial` decimal(8,2) NOT NULL DEFAULT '0.00',
  `total_ventas` decimal(8,2) NOT NULL DEFAULT '0.00',
  `total_declarado` decimal(8,2) NOT NULL DEFAULT '0.00',
  `diferencia` decimal(8,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_corte`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cortes_caja`
--

INSERT INTO `cortes_caja` (`id_corte`, `id_usuario`, `fecha_inicio`, `fecha_cierre`, `monto_inicial`, `total_ventas`, `total_declarado`, `diferencia`) VALUES
(1, 1, '2026-10-21 08:00:00', '2026-10-22 08:43:29', 50.00, 18.00, 79.00, 11.00),
(2, 4, '2026-10-21 21:16:52', '2026-10-21 21:29:51', 300.00, 17.58, 317.58, 0.00),
(3, 1, '2026-10-22 08:43:34', '2026-10-22 08:53:36', 50.00, 88.00, 888.00, 750.00),
(4, 1, '2026-10-22 08:56:12', '2026-10-22 09:04:19', 99.00, 9.12, 3333.00, 3224.88),
(5, 1, '2026-10-22 09:07:06', '2026-10-22 09:07:46', 40.00, 0.00, 500.00, 460.00),
(6, 1, '2026-10-22 09:07:59', '2026-10-22 09:11:46', 50.00, 0.00, 400.00, 350.00),
(7, 5, '2026-10-22 09:12:45', '2026-10-22 09:43:16', 50.00, 9.00, 500.00, 450.00),
(8, 1, '2026-10-22 09:48:12', '2026-10-22 09:55:24', 50.00, 14.00, 1455.00, 1391.00),
(9, 2, '2026-10-22 09:55:48', '2026-10-23 09:23:04', 50.00, 0.00, 50.00, 0.00),
(11, 4, '2026-10-22 11:21:12', '2026-10-22 11:21:40', 5.00, 0.00, 5.00, 0.00),
(12, 5, '2026-10-22 11:21:58', '2026-10-22 11:22:14', 2.00, 0.00, 2.00, 0.00),
(13, 1, '2026-10-22 11:22:41', '2026-10-22 11:57:03', 30.00, 0.00, 30.00, 0.00),
(14, 4, '2026-10-23 09:10:26', '2026-10-23 09:10:49', 50.00, 0.00, 50.00, 0.00),
(15, 4, '2026-10-23 09:11:06', '2026-10-23 09:18:12', 4.00, 36.00, 40.00, 0.00),
(16, 2, '2026-10-23 09:23:13', '2026-10-23 09:23:39', 7.00, 0.00, 7.00, 0.00),
(17, 1, '2026-10-28 09:28:16', '2026-10-28 11:39:43', 8.01, 28.75, 8.01, 0.00),
(18, 1, '2026-10-28 12:01:41', '2026-10-28 12:19:29', 9.00, 29.00, 9.00, 0.00),
(19, 1, '2026-10-29 09:54:31', '2026-10-29 10:04:13', 6.00, 0.00, 6.00, 0.00),
(20, 2, '2026-10-29 10:05:40', '2026-10-30 09:55:39', 2.00, 0.00, 2.00, 0.00),
(21, 1, '2026-10-30 09:36:28', '2026-10-30 09:51:01', 4.00, 0.00, 4.00, 0.00),
(22, 1, '2026-10-31 11:02:11', NULL, 7.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas_dulceria`
--

DROP TABLE IF EXISTS `detalle_ventas_dulceria`;
CREATE TABLE IF NOT EXISTS `detalle_ventas_dulceria` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_venta` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(8,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(8,2) NOT NULL,
  `modificadores` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_venta` (`id_venta`),
  KEY `id_producto` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_ventas_dulceria`
--

INSERT INTO `detalle_ventas_dulceria` (`id_detalle`, `id_venta`, `id_producto`, `cantidad`, `precio_unitario`, `subtotal`, `modificadores`) VALUES
(3, 16, 5, 2, 8.75, 17.50, 'Hot Dogs Con Todo'),
(4, 16, 11, 1, 3.50, 3.50, 'Cereza Roja'),
(5, 16, 14, 1, 4.00, 4.00, 'Con Jalapeños'),
(6, 16, 7, 1, 3.75, 3.75, 'Mantequilla Tradicional'),
(7, 18, 1, 1, 13.00, 13.00, 'Popcorn Caramelo, Extra Mantequilla Líquida');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funciones`
--

DROP TABLE IF EXISTS `funciones`;
CREATE TABLE IF NOT EXISTS `funciones` (
  `id_funcion` int NOT NULL AUTO_INCREMENT,
  `id_pelicula` int NOT NULL,
  `id_sala` int NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `precio_boleto` decimal(8,2) NOT NULL,
  `formato` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2D',
  PRIMARY KEY (`id_funcion`),
  KEY `id_pelicula` (`id_pelicula`),
  KEY `id_sala` (`id_sala`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `funciones`
--

INSERT INTO `funciones` (`id_funcion`, `id_pelicula`, `id_sala`, `fecha_hora`, `precio_boleto`, `formato`) VALUES
(1, 3, 1, '2026-10-22 14:00:00', 4.50, '2D'),
(2, 3, 1, '2026-10-21 14:00:00', 4.50, '2D'),
(3, 3, 1, '2026-10-22 06:30:00', 3.50, '2D'),
(4, 1, 2, '2026-10-21 13:30:00', 4.00, '2D'),
(5, 1, 2, '2026-10-22 14:00:00', 4.00, '2D'),
(6, 1, 2, '2026-10-22 14:00:00', 6.00, '3D'),
(7, 2, 3, '2026-10-21 15:00:00', 4.00, '2D'),
(8, 2, 3, '2026-10-22 14:00:00', 4.00, '2D'),
(9, 2, 3, '2026-10-22 20:00:00', 4.00, '2D'),
(12, 4, 2, '2026-10-22 19:00:00', 4.00, '2D'),
(13, 1, 1, '2026-10-23 14:00:00', 4.00, '2D'),
(14, 1, 1, '2026-10-23 19:00:00', 4.00, '2D'),
(15, 1, 1, '2026-10-23 16:30:00', 4.00, '2D'),
(16, 2, 3, '2026-10-28 14:00:00', 4.00, '2D'),
(17, 2, 3, '2026-10-28 16:30:00', 4.00, '2D'),
(18, 2, 3, '2026-10-28 19:00:00', 4.00, '2D'),
(19, 1, 1, '2026-10-28 14:00:00', 4.00, '2D'),
(20, 1, 1, '2026-10-28 16:30:00', 4.00, '2D'),
(21, 1, 1, '2026-10-28 19:00:00', 4.00, '2D'),
(22, 2, 1, '2026-10-30 14:00:00', 4.00, '2D');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peliculas`
--

DROP TABLE IF EXISTS `peliculas`;
CREATE TABLE IF NOT EXISTS `peliculas` (
  `id_pelicula` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clasificacion` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duracion` int NOT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_pelicula`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `peliculas`
--

INSERT INTO `peliculas` (`id_pelicula`, `titulo`, `clasificacion`, `duracion`, `imagen`) VALUES
(1, 'Gladiador II', 'C', 148, 'gladiador2.jpeg'),
(2, 'Moana 2', 'A', 100, 'Moana2.jpg'),
(3, 'Spider-Man: Beyond the Spider-Verse', 'B', 140, 'Spider-Man-_Beyond_The_Spider-Verse.webp'),
(4, 'Moana 3', 'A', 60, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE IF NOT EXISTS `productos` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'palomitas',
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio` decimal(8,2) NOT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `personalizable` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `stock_disponible` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `categoria`, `descripcion`, `precio`, `imagen`, `personalizable`, `activo`, `stock_disponible`) VALUES
(1, 'Combo Stark Duo', 'combos', '1 Popcorn Grande + 2 Refrescos Medianos + 1 M&Ms.', 11.50, 'img/dulceria/combo_stark.jpg', 1, 1, 49),
(2, 'Combo Pareja Mega', 'combos', '1 Popcorn Mega + 2 Refrescos Grandes + 1 Nachos con queso.', 14.00, 'img/dulceria/combo_pareja.jpg', 1, 1, 50),
(3, 'Combo Individual', 'combos', '1 Popcorn Mediano + 1 Refresco Mediano.', 6.50, 'img/dulceria/combo_individual.jpg', 1, 1, 50),
(4, 'Combo Infantil', 'combos', '1 Popcorn Pequeño + 1 Bebida + 1 Golosina sorpresa.', 5.50, 'img/dulceria/combo_infantil.jpg', 1, 1, 50),
(5, 'Combo Hot Dog', 'combos', '2 Hot Dogs clásicos con aderezos + 2 Refrescos Medianos.', 8.75, 'img/dulceria/combo_hotdog.jpg', 1, 1, 48),
(6, 'Palomitas Grandes', 'palomitas', 'Recipiente grande. Elige sabor (mantequilla, caramelo...) y agrega extras.', 4.50, 'img/dulceria/palomitas.jpg', 1, 1, 100),
(7, 'Palomitas Medianas', 'palomitas', 'Porción mediana recién reventadas con sabor y extras a elección.', 3.75, 'img/dulceria/palomitas.jpg', 1, 1, 99),
(8, 'Palomitas Pequeñas', 'palomitas', 'Porción personal clásica recién hecha.', 2.75, 'img/dulceria/palomitas.jpg', 1, 1, 100),
(9, 'Refresco Grande (32 oz)', 'bebidas', 'Vaso grande con hielo y bebida gasificada a elección.', 3.00, 'img/dulceria/refresco.jpg', 1, 1, 150),
(10, 'Refresco Mediano (22 oz)', 'bebidas', 'Vaso mediano con hielo a elección.', 2.50, 'img/dulceria/refresco.jpg', 1, 1, 150),
(11, 'Icee Granizado Grande', 'bebidas', 'Granizado helado refrescante sabor cereza o frambuesa.', 3.50, 'img/dulceria/icee.jpg', 1, 1, 79),
(12, 'Agua Embotellada (600 ml)', 'bebidas', 'Agua pura purificada sin gas.', 1.75, 'img/dulceria/agua.jpg', 0, 1, 100),
(13, 'Té Frío (600 ml)', 'bebidas', 'Té helado saborizado con rodaja de limón.', 2.25, 'img/dulceria/te_frio.jpg', 1, 1, 80),
(14, 'Nachos con Queso Cheddar', 'snacks', 'Totopos crujientes con queso cheddar caliente.', 4.00, 'img/dulceria/nachos.jpg', 1, 1, 79),
(15, 'Hot Dog Clásico Americano', 'snacks', 'Salchicha de res con salsas tradicionales.', 3.50, 'img/dulceria/hotdog.jpg', 1, 1, 80),
(16, 'M&Ms Maní Pack Cine (120g)', 'dulces', 'Chocolates confitados rellenos de maní tostado.', 2.50, 'img/dulceria/mms_mani.jpg', 0, 1, 100),
(17, 'Skittles Frutales Original', 'dulces', 'Caramelos masticables surtidos de sabores cítricos y frutales.', 2.00, 'img/dulceria/skittles.jpg', 0, 1, 100),
(18, 'Snickers Barra King Size', 'dulces', 'Barra de chocolate, turrón, caramelo y maní.', 2.00, 'img/dulceria/snickers.jpg', 0, 1, 100),
(19, 'Gomitas Ácidas Sour Patch', 'dulces', 'Gomitas con toque ácido y centro frutal dulce.', 2.25, 'img/dulceria/sour_patch.jpg', 0, 1, 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador', 'Control total del sistema, reportes y configuración'),
(2, 'Cajero', 'Venta en taquilla, dulcería y arqueo de caja');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salas`
--

DROP TABLE IF EXISTS `salas`;
CREATE TABLE IF NOT EXISTS `salas` (
  `id_sala` int NOT NULL AUTO_INCREMENT,
  `nombre_sala` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad` int NOT NULL,
  PRIMARY KEY (`id_sala`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `salas`
--

INSERT INTO `salas` (`id_sala`, `nombre_sala`, `capacidad`) VALUES
(1, 'Sala 1 - Principal', 110),
(2, 'Sala 2 - Macro', 110),
(3, 'Sala 3 - Estándar', 110);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `id_rol` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Activo',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuario` (`usuario`),
  KEY `id_rol` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `nombre`, `usuario`, `password`, `estado`) VALUES
(1, 1, 'Administrador General', 'admin', '12345', 'Activo'),
(2, 2, 'Luis Cruz', 'luis', '12345', 'Activo'),
(4, 1, 'Diego Rivas', 'diego', '12345', 'Activo'),
(5, 2, 'Maria Lopez', 'maria', 'maricarmen', 'Activo'),
(6, 2, 'Carlos Ramirez', 'carlos', '12345', 'Inactivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE IF NOT EXISTS `ventas` (
  `id_venta` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(8,2) NOT NULL,
  `metodo_pago` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_venta` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'boleteria',
  PRIMARY KEY (`id_venta`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_usuario`, `fecha_hora`, `total`, `metodo_pago`, `tipo_venta`) VALUES
(1, 1, '2026-10-21 20:55:52', 18.00, 'Efectivo', 'boleteria'),
(2, 4, '2026-10-21 21:05:21', 27.00, 'Efectivo', 'boleteria'),
(3, 4, '2026-10-21 21:06:59', 14.65, 'Tarjeta', 'boleteria'),
(4, 4, '2026-10-21 21:17:25', 17.58, 'Efectivo', 'boleteria'),
(5, 1, '2026-10-22 08:52:08', 88.00, 'Efectivo', 'boleteria'),
(6, 5, '2026-10-22 08:54:57', 5.20, 'Efectivo', 'boleteria'),
(7, 1, '2026-10-22 08:56:32', 9.12, 'Efectivo', 'boleteria'),
(8, 1, '2026-10-22 09:06:20', 8.00, 'Efectivo', 'boleteria'),
(9, 5, '2026-10-22 09:15:41', 9.00, 'Tarjeta', 'boleteria'),
(10, 1, '2026-10-22 09:45:36', 12.00, 'Efectivo', 'boleteria'),
(11, 1, '2026-10-22 09:48:58', 14.00, 'Efectivo', 'boleteria'),
(13, 4, '2026-10-23 09:12:03', 12.00, 'Efectivo', 'boleteria'),
(14, 4, '2026-10-23 09:15:31', 24.00, 'Efectivo', 'boleteria'),
(16, 1, '2026-10-28 11:38:30', 28.75, 'Tarjeta', 'dulceria'),
(17, 1, '2026-10-28 12:18:00', 16.00, 'Tarjeta', 'boleteria'),
(18, 1, '2026-10-28 12:18:20', 13.00, 'Tarjeta', 'dulceria');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `boletos`
--
ALTER TABLE `boletos`
  ADD CONSTRAINT `boletos_ibfk_1` FOREIGN KEY (`id_funcion`) REFERENCES `funciones` (`id_funcion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `boletos_ibfk_2` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cortes_caja`
--
ALTER TABLE `cortes_caja`
  ADD CONSTRAINT `cortes_caja_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_ventas_dulceria`
--
ALTER TABLE `detalle_ventas_dulceria`
  ADD CONSTRAINT `detalle_ventas_dulceria_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_ventas_dulceria_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `funciones`
--
ALTER TABLE `funciones`
  ADD CONSTRAINT `funciones_ibfk_1` FOREIGN KEY (`id_pelicula`) REFERENCES `peliculas` (`id_pelicula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `funciones_ibfk_2` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id_sala`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
