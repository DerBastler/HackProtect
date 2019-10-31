CREATE TABLE `blacklist` (
  `ID` int(10) NOT NULL,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `IP` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `URI` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `useragent` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `hits` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `blacklist` ADD PRIMARY KEY (`ID`);
ALTER TABLE `blacklist` MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;


CREATE TABLE `Stats` (
  `ID` int(10) NOT NULL,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `IP` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `host` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Site` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `countryName` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `continent` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_prov` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `zipcode` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `isp` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `browser` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `os` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Stats` ADD PRIMARY KEY (`ID`);
ALTER TABLE `Stats` MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

CREATE TABLE `LoginAttempt` (
  `ID` int(10) NOT NULL,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `IP` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `LoginAttempt` ADD PRIMARY KEY (`ID`);
ALTER TABLE `LoginAttempt` MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;