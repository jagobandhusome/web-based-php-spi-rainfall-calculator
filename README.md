# PHP Standardized Precipitation Index (SPI) Calculator

Yearly **SPI** (Standardized Precipitation Index) calculator. Upload monthly rainfall as a tab-separated text file; the app fits a gamma distribution and reports one SPI value per year.

Originally deployed as [spi.booleandreams.com](https://spi.booleandreams.com/). Built for SPARRSO rainfall analysis.

This is **not** the Banglalink MSISDN checker. That is a different backup (`sumon.booleandreams.com` / `smart.aloasbei.com`).

---

## What it does

1. Upload a text file: `year`, `month`, `rain` (tab-separated).
2. Wipe previous runs and store rows in MySQL table `data`.
3. Group rainfall by year (sum, month count, mean, ln of mean).
4. Estimate gamma shape / scale (`k5` … `k10`), matching the Excel formulas shown on the report page.
5. For each year: `GAMMADIST(mean, k9, k10, TRUE)` then `NORMINV(gamma, 0, 1)` → **SPI**.
6. Show station constants and the yearly table.

```
uploads/input.txt  (year \t month \t rain)
        │
        ▼
   index.php     TRUNCATE + INSERT into data / spical
        │
        ▼
   spi.php       yearly stats + gamma + SPI
        │
        ▼
   table.php     report (k-values + yearly SPI)
```

Method follows the usual McKee-style SPI idea: fit precipitation to a gamma distribution, then transform the cumulative probability to a standard normal z-score. This build is a **yearly** SPI (one value per calendar year from monthly rows), not a rolling 1 / 3 / 6 / 12-month window.

---

## Screens

| File | Menu | Role |
| --- | --- | --- |
| `index.php` | New Calculation | Upload rainfall file, load MySQL |
| `spi.php` | Calculate | Run gamma + SPI (then redirect to report) |
| `table.php` | Report | Print k-values and yearly results |

There is **no login**. Anyone who can open the URL can upload data and overwrite the last calculation.

---

## Tech stack

| Piece | Detail |
| --- | --- |
| Language | PHP (mysqli) |
| Database | MySQL |
| Math | `Gamma.php` — Excel-like `GAMMADIST` and `NORMINV` (incomplete gamma + Acklam inverse normal) |
| UI | Bootstrap 3, Font Awesome, custom CSS |
| Sample data | `uploads/input.txt` (monthly rain from 1961) |

---

## Requirements

- PHP 5.6+ or 7.x with `mysqli`, `mbstring`
- MySQL / MariaDB
- Apache (or any PHP host) with this folder as document root
- Writable MySQL user for `TRUNCATE` / `INSERT` / `UPDATE`

---

## Input file format

Tab-separated text. No header row. Three columns:

| Column | Meaning | Example |
| --- | --- | --- |
| 1 | Year | `1961` |
| 2 | Month (1–12) | `6` |
| 3 | Rainfall | `1081` |

Example (`uploads/input.txt`):

```
1961	1	2
1961	2	0
1961	3	18
1961	6	1081
```

A full year does not have to have 12 months. `countableMonth` is how many numeric rain rows that year had. Non-numeric rain values are skipped when yearly totals are built.

---

## Calculation (same as the Excel notes on the report)

After grouping by year:

| Field | Meaning |
| --- | --- |
| Total yearly rain | Sum of `rain` for that year |
| Countable month | Number of numeric months |
| Average yearly rain (mean) | total / count |
| Log of mean | `ln(mean)` |

Station-wide constants (table `kval`):

| Code | Excel-style formula | Meaning |
| --- | --- | --- |
| `k5` | `AVERAGE` of yearly means | Mean of means |
| `k6` | `SUM` of `ln(mean)` | Sum of logs |
| `k8` | `LN(k5) - k6 / n` | `A` in the gamma fit (`n` = number of years) |
| `k9` | `(1/(4*k8))*(1+SQRT(1+4*k8/3))` | Gamma shape |
| `k10` | `k5 / k9` | Gamma scale |

Per year:

```
gammaValue = GAMMADIST(yearlyMean, k9, k10, TRUE)
spiVal     = NORMINV(gammaValue, 0, 1)
```

Rough SPI reading (McKee):

| SPI | Condition |
| --- | --- |
| ≥ 2.0 | Extremely wet |
| 1.5 to 1.99 | Very wet |
| 1.0 to 1.49 | Moderately wet |
| −0.99 to 0.99 | Near normal |
| −1.49 to −1.0 | Moderately dry |
| −1.99 to −1.5 | Severely dry |
| ≤ −2.0 | Extremely dry |

---

## Database

Create a database and run:

```sql
CREATE TABLE `data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL,
  `month` int(11) DEFAULT NULL,
  `rain` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `spical` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL,
  `totalYearlyRain` double DEFAULT NULL,
  `countableMonth` int(11) DEFAULT NULL,
  `yearlyAvgRain` double DEFAULT NULL,
  `ln` double DEFAULT NULL,
  `gammaValue` double DEFAULT NULL,
  `spiVal` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `kval` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `k5` double DEFAULT NULL,
  `k6` double DEFAULT NULL,
  `k8` double DEFAULT NULL,
  `k9` double DEFAULT NULL,
  `k10` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

The live backup used database `boolkyos_spi`. Change the `mysqli_connect(...)` calls in all three PHP pages to your own host, user, password, and database:

- `index.php`
- `spi.php`
- `table.php`

There is no shared `config.php`. Uploading a new file **truncates** `data` and `spical`. Calculate **truncates** `kval` then writes one new row.

---

## How to run a calculation

1. Open `/` (`index.php`) → **Choose a text file** → **Submit**.
2. Open **Calculate** (`spi.php`) if the browser did not redirect. This fills `spical` and `kval`.
3. Open **Report** (`table.php`) for k-values and the yearly SPI table.

A sample file is already in `uploads/input.txt`.

---

## Project layout

```
.
├── index.php              # upload rainfall file
├── spi.php                # yearly gamma + SPI
├── table.php              # report
├── Gamma.php              # GAMMADIST / NORMINV
├── uploads/input.txt      # sample monthly rain
├── css/                   # Bootstrap, Font Awesome, custom
├── scripts/bootstrap.min.js
├── robots.txt
└── README.md
```

---

## Security notes

- Move MySQL credentials out of the PHP files before you publish this repo. They are hardcoded in `index.php`, `spi.php`, and `table.php`.
- The upload handler does not check file type or size beyond “a tmp file exists”. Restrict who can reach the site (HTTP auth or VPN).
- Inserts are not parameterized. Use a trusted rainfall file only.
- `robots.txt` disallows all crawlers (`Disallow: /`).

---

## Known limits

- Yearly SPI only; not 1 / 3 / 6 / 12-month SPI.
- `index.php` calls `$yearSql->fetchAll()` on a mysqli statement (PDO-style). On some PHP/mysqli setups year rows never land in `spical` until you fix that (use `get_result()` / `fetch_all` instead).
- `spi.php` redirects to `https://spi.booleandream.com/spi/table.php` (host typo and extra `/spi/`). After calculate, open `/table.php` yourself if the redirect 404s.
- Menu links on `index.php` omit `https://` (`spi.booleandreams.com/spi.php`).
- Zero rainfall makes `ln(mean)` fail if a year’s mean is 0. Skip or replace zeros the way your Excel sheet did.
- `print_r($yearValues)` on upload is debug output.
- No export (CSV/Excel) of the report table.

---

## License

No license file is included. Add one if you make the repository public.
