-- phpMyAdmin SQL Dump
-- version 4.3.6
-- http://www.phpmyadmin.net
--
-- Client :  sql1.olympe.in
-- Généré le :  Lun 12 Janvier 2015 à 11:37
-- Version du serveur :  5.5.40-MariaDB-1~saucy
-- Version de PHP :  5.5.3-1ubuntu2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `K1Cq5R2m`
--
CREATE DATABASE IF NOT EXISTS `K1Cq5R2m` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `K1Cq5R2m`;

-- --------------------------------------------------------

--
-- Structure de la table `bac`
--

DROP TABLE IF EXISTS `bac`;
CREATE TABLE IF NOT EXISTS `bac` (
  `ID` int(11) NOT NULL,
  `Nature` varchar(30) NOT NULL,
  `Temperature` varchar(30) NOT NULL,
  `Details` varchar(30) NOT NULL,
  `Id_exposant` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `especes`
--

DROP TABLE IF EXISTS `especes`;
CREATE TABLE IF NOT EXISTS `especes` (
  `ID` int(11) NOT NULL,
  `Designation` varchar(50) NOT NULL,
  `Taille` int(11) NOT NULL,
  `Prix` int(11) NOT NULL,
  `Quantite` int(11) NOT NULL,
  `Eau` varchar(10) NOT NULL,
  `Id_exposant` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=160 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `exposant`
--

DROP TABLE IF EXISTS `exposant`;
CREATE TABLE IF NOT EXISTS `exposant` (
  `ID` int(11) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `Prenom` varchar(50) NOT NULL,
  `Adresse` varchar(50) NOT NULL,
  `Adresse2` varchar(50) NOT NULL,
  `CodePostal` varchar(5) NOT NULL,
  `Ville` varchar(50) NOT NULL,
  `Telephone` varchar(15) NOT NULL,
  `Portable` varchar(15) NOT NULL,
  `Mail` varchar(50) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `Confirmation` tinyint(1) NOT NULL DEFAULT '0',
  `Commentaire` text NOT NULL,
  `Date` date NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `Id` varchar(30) NOT NULL,
  `Mail` varchar(50) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `UserLevel` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `bac`
--
ALTER TABLE `bac`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `especes`
--
ALTER TABLE `especes`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `exposant`
--
ALTER TABLE `exposant`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `bac`
--
ALTER TABLE `bac`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT pour la table `especes`
--
ALTER TABLE `especes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=160;
--
-- AUTO_INCREMENT pour la table `exposant`
--
ALTER TABLE `exposant`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
