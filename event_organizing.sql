SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `users`;


-- untuk tabel users
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,

  -- buat aktivasi akun 
  `aktivasi` VARCHAR(255) DEFAULT NULL,
  `kadaluarsa` DATETIME DEFAULT NULL,
  `status_akun` TINYINT(1) NOT NULL DEFAULT 0,

  -- buat reset password
  `reset_token` VARCHAR(255) DEFAULT NULL,
  `reset_expired` DATETIME DEFAULT NULL,

  `nama_lengkap` VARCHAR(255) DEFAULT NULL,

  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- untuk tabel event kegiatan 
CREATE TABLE `events` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,       -- EO pemilik kegiatan
  `nama_event` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `tanggal_event` DATE NOT NULL,
  `lokasi` VARCHAR(255) DEFAULT NULL,

  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),

  -- buat ke tabel users
  CONSTRAINT `fk_user_event`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
