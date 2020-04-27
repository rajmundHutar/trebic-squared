# [Třebíč²](https://trebicsquared.cz)

App for [Třebíč²](https://trebicsquared.cz) game. 

Guessing game with images in squared grid on map.

---

App is written in PHP in [Nette](https://nette.org) framework and uses MySQL DB.

---

#### Prerequisites
- Apache
- MySQL
- Composer

---

### Instalace
1. Clone repo: `git clone git@github.com:rajmundHutar/trebic-squared.git`
1. Install dependencies: run `composer install` in `trebic-squared` folder.
2. Make directories writeable: `temp/`, `log/`, `www/images/answers`, `www/images/questions` (eg. `chmod -R 0777 temp/ log/ www/images/answers www/images/questions`).
3. Create DB, scheme is in: `sql/the_game.sql`.
4. Setup user and pass to DB in: `app/config/local.neon`.
5. Open web browser: (např. `http://localhost/trebic-squared/`)
6. Register and in DB in table `user` set `role` = 'admin' to admin user.