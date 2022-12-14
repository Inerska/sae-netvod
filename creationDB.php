<?php

use Application\datalayer\factory\ConnectionFactory;

require_once 'vendor/autoload.php';

ConnectionFactory::setConfig( 'db.config.ini' );
$db = ConnectionFactory::getConnection();

$query = $db->prepare(
<<<END
SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
END);
$query->execute();

$query = $db->prepare(
<<<END
DROP TABLE IF EXISTS episode;
CREATE TABLE episode (
  id int(11) NOT NULL AUTO_INCREMENT,
  numero int(11) NOT NULL DEFAULT 1,
  titre varchar(128) NOT NULL,
  resume text DEFAULT NULL,
  duree int(11) NOT NULL DEFAULT 0,
  file varchar(256) DEFAULT NULL,
  serie_id int(11) DEFAULT NULL,
  PRIMARY KEY (id)
)
END);
$query->execute();

$query = $db->prepare(
<<<END
INSERT INTO episode (id, numero, titre, resume, duree, file, serie_id) VALUES
(1,	1,	'Le lac',	'Le lac se révolte ',	8,	'lake.mp4',	1),
(2,	2,	'Le lac : les mystères de l\'eau trouble',	'Un grand mystère, l\'eau du lac est trouble. Jack trouvera-t-il la solution ?',	8,	'lake.mp4',	1),
(3,	3,	'Le lac : les mystères de l\'eau sale',	'Un grand mystère, l\'eau du lac est sale. Jack trouvera-t-il la solution ?',	8,	'lake.mp4',	1),
(4,	4,	'Le lac : les mystères de l\'eau chaude',	'Un grand mystère, l\'eau du lac est chaude. Jack trouvera-t-il la solution ?',	8,	'lake.mp4',	1),
(5,	5,	'Le lac : les mystères de l\'eau froide',	'Un grand mystère, l\'eau du lac est froide. Jack trouvera-t-il la solution ?',	8,	'lake.mp4',	1),
(6,	1,	'Eau calme',	'L\'eau coule tranquillement au fil du temps.',	15,	'water.mp4',	2),
(7,	2,	'Eau calme 2',	'Le temps a passé, l\'eau coule toujours tranquillement.',	15,	'water.mp4',	2),
(8,	3,	'Eau moins calme',	'Le temps des tourments est pour bientôt, l\'eau s\'agite et le temps passe.',	15,	'water.mp4',	2),
(9,	4,	'la tempête',	'C\'est la tempête, l\'eau est en pleine agitation. Le temps passe mais rien n\'y fait. Jack trouvera-t-il la solution ?',	15,	'water.mp4',	2),
(10,	5,	'Le calme après la tempête',	'La tempête est passée, l\'eau retrouve son calme. Le temps passe et Jack part en vacances.',	15,	'water.mp4',	2),
(11,	1,	'les chevaux s\'amusent',	'Les chevaux s\'amusent bien, ils ont apportés les raquettes pour faire un tournoi de badmington.',	7,	'horses.mp4',	3),
(12,	2,	'les chevals fous',	'- Oh regarde, des beaux chevals !!\r\n- non, des chevaux, des CHEVAUX !\r\n- oh, bin ça alors, ça ressemble drôlement à des chevals ?!!?',	7,	'horses.mp4',	3),
(13,	3,	'les chevaux de l\'étoile noire',	'Les chevaux de l\'Etoile Noire débrquent sur terre et mangent toute l\'herbe !',	7,	'horses.mp4',	3),
(14,	1,	'Tous à la plage',	'C\'est l\'été, tous à la plage pour profiter du soleil et de la mer.',	18,	'beach.mp4',	4),
(15,	2,	'La plage le soir',	'A la plage le soir, il n\'y a personne, c\'est tout calme',	18,	'beach.mp4',	4),
(16,	3,	'La plage le matin',	'A la plage le matin, il n\'y a personne non plus, c\'est tout calme et le jour se lève.',	18,	'beach.mp4',	4),
(17,	1,	'champion de surf',	'Jack fait du surf le matin, le midi le soir, même la nuit. C\'est un pro.',	11,	'surf.mp4',	5),
(18,	2,	'surf détective',	'Une planche de surf a été volée. Jack mène l\'enquête. Parviendra-t-il à confondre le brigand ?',	11,	'surf.mp4',	5),
(19,	3,	'surf amitié',	'En fait la planche n\'avait pas été volée, c\'est Jim, le meilleur ami de Jack, qui lui avait fait une blague. Les deux amis partagent une menthe à l\'eau pour célébrer leur amitié sans faille.',	11,	'surf.mp4',	5),
(20,	1,	'Ça roule, ça roule',	'Ça roule, ça roule toute la nuit. Jack fonce dans sa camionnette pour rejoindre le spot de surf.',	27,	'cars-by-night.mp4',	6),
(21,	2,	'Ça roule, ça roule toujours',	'Ça roule la nuit, comme chaque nuit. Jim fonce avec son taxi, pour rejoindre Jack à la plage. De l\'eau a coulé sous les ponts. Le mystère du Lac trouve sa solution alors que les chevaux sont de retour après une virée sur l\'Etoile Noire.',	27,	'cars-by-night.mp4',	6);
END);
$query->execute();

$query = $db->prepare(
    <<<END
DROP TABLE IF EXISTS serie;
CREATE TABLE serie (
  id int(11) NOT NULL AUTO_INCREMENT,
  titre varchar(128) NOT NULL,
  descriptif text NOT NULL,
  img varchar(256) NOT NULL,
  annee int(11) NOT NULL,
  date_ajout date NOT NULL,
  note_moyenne float(5),
  nombre_note int(5),
  PRIMARY KEY (id)
)
END);
$query->execute();

$query = $db->prepare(
    <<<END
INSERT INTO serie (id, titre, descriptif, img, annee, date_ajout, note_moyenne, nombre_note) VALUES
(1,	'Le lac aux mystères',	'C\'est l\'histoire d\'un lac mystérieux et plein de surprises. La série, bluffante et haletante, nous entraine dans un labyrinthe d\'intrigues époustouflantes. A ne rater sous aucun prétexte !',	'images/1.jpg',	2020,	'2022-10-30', 2.5, 2),
(2,	'L\'eau a coulé',	'Une série nostalgique qui nous invite à revisiter notre passé et à se remémorer tout ce qui s\'est passé depuis que tant d\'eau a coulé sous les ponts.',	'images/2.jfif',	1907,	'2022-10-29', 0, 0),
(3,	'Chevaux fous',	'Une série sur la vie des chevals sauvages en liberté. Décoiffante.',	'images/3.jfif',	2017,	'2022-10-31', 3, 1),
(4,	'A la plage',	'Le succès de l\'été 2021, à regarder sans modération et entre amis.',	'images/4.jfif',	2021,	'2022-11-04', 2.5, 2),
(5,	'Champion',	'La vie trépidante de deux champions de surf, passionnés dès leur plus jeune age. Ils consacrent leur vie à ce sport. ',	'images/5.jfif',	2022,	'2022-11-03', 0, 0),
(6,	'Une ville la nuit',	'C\'est beau une ville la nuit, avec toutes ces voitures qui passent et qui repassent. La série suit un livreur, un chauffeur de taxi, et un insomniaque. Tous parcourent la grande ville une fois la nuit venue, au volant de leur véhicule.',	'images/6.jfif',	2017,	'2022-10-31', 5, 1);
END);
$query->execute();

$query = $db->prepare(
    <<<END
DROP TABLE IF EXISTS user;
CREATE TABLE user (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(128) NOT NULL,
  passwrd varchar(128) NOT NULL,
  role int(5) NOT NULL,
  active boolean,
  activationToken varchar(64),
  activationExpiration int(16),
  renewToken varchar(64),
  renewExpiration int(16),
  PRIMARY KEY (id)
)
END
);
$query->execute();

$query = $db->prepare(
    <<<END
INSERT INTO user (email, passwrd, role, active) VALUES
('user1@gmail.com', '\$2y\$12\$e9DCiDKOGpVs9s.9u2ENEOiq7wGvx7sngyhPvKXo2mUbI3ulGWOdC', 1, true),
('user2@gmail.com', '\$2y\$12\$4EuAiwZCaMouBpquSVoiaOnQTQTconCP9rEev6DMiugDmqivxJ3AG', 1, true),
('user3@gmail.com', '\$2y\$12\$5dDqgRbmCN35XzhniJPJ1ejM5GIpBMzRizP730IDEHsSNAu24850S', 1, true),
('admin@gmail.com', '\$2y\$12\$JtV1W6MOy/kGILbNwGR2lOqBn8PAO3Z6MupGhXpmkeCXUPQ/wzD8a', 100, true);
END);
$query->execute();

$query = $db->prepare(
    <<<END
DROP TABLE IF EXISTS profil;
CREATE TABLE profil (
  idProfil int(5) NOT NULL AUTO_INCREMENT,
  nom varchar(64),
  prenom varchar(64),
  age int(3),
  sexe varchar(32),
  genrePref varchar(64),
  PRIMARY KEY (idProfil)
)
END);
$query->execute();

$query = $db->prepare(
    <<<END
INSERT INTO profil (nom, prenom, age, sexe, genrePref) VALUES
('Scher', 'Adrien', 19, '', ''),
('Gridel', 'Alexis', 21, '', ''),
('Grosmann', 'Jeremy', 19, '', ''),
('Povoas', 'Florian', 19, '', '')
END);
$query->execute();


$query = $db->prepare(
    <<<END
DROP TABLE IF EXISTS genre;
CREATE TABLE genre (
  idGenre int(5) NOT NULL AUTO_INCREMENT,
  libelle varchar(64) NOT NULL,
  PRIMARY KEY (idGenre)
)
END);
$query->execute();

$query = $db->prepare(
    <<<END
DROP TABLE IF EXISTS type;
CREATE TABLE type (
  idType int(5) NOT NULL AUTO_INCREMENT,
  libelle varchar(64) NOT NULL,
  PRIMARY KEY (idType)
)
END);
$query->execute();

$query = $db->prepare(
    <<<END
INSERT INTO genre (libelle) VALUES
('Action'),
('Aventure'),
('Comédie'),
('Drame'),
('Fantastique'),
('Horreur'),
('Policier'),
('Romance'),
('Science-Fiction'),
('Thriller');
END);
$query->execute();

$query = $db->prepare(
    <<<END
INSERT INTO type (libelle) VALUES
('Adolescent'),
('Adulte'),
('Enfant'),
('Tout public');
END);
$query->execute();

$query = $db->prepare(
    <<<END
DROP TABLE IF EXISTS serie_genre;
CREATE TABLE serie_genre (
  idSerie int(5) NOT NULL,
  idGenre int(5) NOT NULL,
  PRIMARY KEY (idSerie, idGenre)
)
END);
$query->execute();

$query = $db->prepare(
    <<<END
DROP TABLE IF EXISTS serie_type;
CREATE TABLE serie_type (
  idSerie int(5) NOT NULL,
  idType int(5) NOT NULL,
  PRIMARY KEY (idSerie, idType)
)
END);
$query->execute();

$query = $db->prepare(
    <<<END
INSERT INTO serie_genre VALUES 
(1, 1),
(1, 4),
(1, 7),
(1, 10),
(2, 2),
(2, 3),
(2, 4),
(2, 8),
(2, 9),
(3, 1),
(3, 3),
(3, 5),
(3, 6),
(3, 9),
(4, 3),
(4, 7),
(4, 8),
(5, 1),
(5, 4),
(5, 8),
(6, 1),
(6, 2),
(6, 5),
(6, 6),
(6, 7),
(6, 8),
(6, 9),
(6, 10)
END);
$query->execute();

$query = $db->prepare(
    <<<END
INSERT INTO serie_type VALUES 
(1, 1),
(1, 2),
(2, 4),
(3, 3),
(4, 4),
(5, 1),
(5, 3),
(6, 2)
END);
$query->execute();

$query = $db->prepare(
    <<<END
DROP TABLE IF EXISTS user_serie_pref;
CREATE TABLE user_serie_pref (
  idUser int(11) NOT NULL,
  idSerie int(5) NOT NULL,
  PRIMARY KEY (idUser, idSerie)
)
END);
$query->execute();

$query = $db->prepare(
    <<<END
INSERT INTO user_serie_pref VALUES 
(1, 1),
(1, 2),
(1, 5),
(2, 2),
(2, 4),
(2, 5),
(3, 1),
(3, 3),
(3, 4),
(3, 6),
(4, 1),
(4, 4),
(4, 6)
END);
$query->execute();

$query = $db->prepare(
    <<<END
DROP TABLE IF EXISTS user_serie;
DROP TABLE IF EXISTS user_serie_vu;
CREATE TABLE user_serie_vu (
  idUser int(11) NOT NULL,
  idSerie int(5) NOT NULL,
  PRIMARY KEY (idUser, idSerie)
)
END);
$query->execute();

$query = $db->prepare(
    <<<END
INSERT INTO user_serie_vu VALUES 
(1, 1),
(1, 4),
(2, 2),
(2, 3),
(3, 3),
(3, 4),
(4, 1)
END);
$query->execute();

$query = $db->prepare(
    <<<END
DROP TABLE IF EXISTS user_serie_en_cours;
CREATE TABLE user_serie_en_cours (
  idUser int(11) NOT NULL,
  idSerie int(5) NOT NULL,
  numEpisode int(3) NOT NULL,
  PRIMARY KEY (idUser, idSerie)
)
END);
$query->execute();

$query = $db->prepare(
    <<<END
INSERT INTO user_serie_en_cours VALUES 
(1, 2, 3),
(1, 3, 2),
(2, 4, 1),
(2, 5, 2),
(3, 1, 4),
(4, 3, 1),
(4, 6, 1)
END);
$query->execute();

$query = $db->prepare(
    <<<END
DROP TABLE IF EXISTS notation;
CREATE TABLE notation (
  idUser int(11) NOT NULL,
  idSerie int(5) NOT NULL,
  note int(2) NOT NULL,
  commentaire varchar(256),
  PRIMARY KEY (idUser, idSerie)
)
END);
$query->execute();

$query = $db->prepare(
    <<<END
INSERT INTO notation VALUES 
(1, 1, 4, 'Un super film qui donne envie de voir la suite !!!'),
(1, 4, 2, 'Film décevant malgrès la hype autour du film'),
(2, 3, 3, 'Un film sans prétention.'),
(2, 4, 3, 'Un film moyen qui se regarde tranquille.'),
(3, 1, 1, 'Déconseille fortement, passez votre chemin.'),
(4, 6, 5, 'Meilleur film de l\'année 2017, je vous conseille de d\'aller le voir immédiatement')
END);
$query->execute();