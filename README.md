# Digital Game Distribution Platform

An open-source digital game distribution platform built with PHP and MySQL, inspired by Steam and itch.io.

Developed as part of the **Open Source Software Development** course.

## Contributors

- Nguyễn Thành Đạt
- Nguyễn Thị Ngọc Thảo
- Nguyễn Võ Hoàng Anh Thư
- Hồ Xuân Phúc

## Download
[https://github.com/nguyen-th-dat/Digital-Game-Distribution-Platform/releases/latest](https://github.com/nguyen-th-dat/Digital-Game-Distribution-Platform/releases/latest)

## Requirements

- PHP 8.2 or newer
- MySQL
- Composer 2.x
- [XAMPP](https://www.apachefriends.org/download.html) (Apache and MySQL)
- PHP extensions: `mbstring`, `openssl`

## Installation

1. Clone or download the project into the XAMPP `htdocs` directory.
2. Start **Apache** and **MySQL** from XAMPP Control Panel.
3. Open phpMyAdmin and create the project database using the `utf8mb4_unicode_ci` collation.
4. Import the SQL file containing the complete database schema and data from the `database/` directory.
5. Check the database configuration in `config/config.php`.
6. Install dependencies from the project root:

   ```bash
   composer install
   ```

7. Configure email by copying the example file:

   ```bash
   copy config\mail.example.php config\mail.php
   ```

   Update the SMTP settings in `config/mail.php` to enable password-reset emails.

8. Open the application:

   ```text
   http://localhost/<project-folder>/
   ```

9. Register an account to get started.

**Note:** Keep `public/uploads/` intact because the database references images stored in this directory.

## Appearance

The site has a light and a dark theme, and light is the default. The button in the top bar switches between them; the choice is saved to your account when you are signed in, and in a cookie for guests. You can also pick a theme under `Settings → Privacy and preferences`.

Installations that were set up before the theme was added need one extra statement, because `database/du_lieu.sql` already ships with the column:

```bash
mysql -u root -p game_distribution_platform_db < database/migration_giao_dien.sql
```

## Languages

The interface is available in Vietnamese and English. The globe button in the top bar switches the language; the choice is saved to your account when you are signed in, and in a cookie for guests. You can also pick a language under `Settings → Privacy and preferences`.

The wording lives in the `translation/` directory:

- `translation/vi.json` — the Vietnamese catalogue, the default language of the interface.
- `translation/en.json` — the English catalogue. An empty entry means "not translated yet", and the Vietnamese wording is shown instead, so a half-finished translation never blanks a page.
- `translation/en.pending.json` — the list of the keys that still need an English translation, together with the Vietnamese source text. Copy the values into `translation/en.json` to finish them.

The keys are namespaced by area (`nav.*`, `home.*`, `admin.*`, `label.*`, …). Inside the code they are read with the `t()` and `te()` helpers:

```php
flash('success', t('cart.da.cap.nhat.gio.hang'));
```

The label groups of the interface (`label.game_status.published`, `label.pricing.free`, …) are read with `label()` and `labels()`:

```php
echo e(label('game_status', $game['trang_thai']));
```

## Demo accounts

`database/du_lieu.sql` ships with a complete set of sample accounts. Every one of them uses the same password:

```text
a1234567
```

Sign in at `http://localhost/<project-folder>/auth/login` with the username (or the email address) from the table below:

| Username | Email | Roles | What it demonstrates |
|---|---|---|---|
| `admin_test` | `admin_test@test.local` | player, admin | Full administration area: user management, game moderation, audits, reports, payouts |
| `mod_test` | `mod_test@test.local` | player, moderator | Moderation queue, content review, report handling |
| `dev_test` | `dev_test@test.local` | player, developer | Developer dashboard, own games, releases, download links, sales and payouts |
| `player_test` | `player_test@test.local` | player | Store browsing, cart, library, reviews, comments, forums |
| `demo_dev` | `demo_dev@mau.local` | developer | Minh Khang Studio — owner of the sample project, sees every download link and its moderation state |
| `demo_player` | `demo_player@mau.local` | player | Bảo An — already owns the sample game, so the download links are visible |
| `demo_mirror` | `demo_mirror@mau.local` | player | Chí Dũng — submitted a mirror that is still waiting for the developer to approve it |

## Admin Access

To grant administrator privileges, run the following SQL in phpMyAdmin after registering an account. Replace `YOUR_USERNAME` with the account's username.

```sql
INSERT IGNORE INTO nguoi_dung_vai_tro (nguoi_dung_id, vai_tro_id)
SELECT u.ma, r.ma
FROM nguoi_dung u
JOIN vai_tro r ON r.ma_vai_tro = 'admin'
WHERE u.ten_dang_nhap = 'YOUR_USERNAME';
```
