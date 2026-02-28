# Render.com par Deploy karne ke steps

## 1. GitHub par code push karein

```bash
cd C:\xampp\htdocs\school-management-system
git init
git add .
git commit -m "Initial commit for deployment"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/school-management-system.git
git push -u origin main
```

## 2. Render par account aur PostgreSQL

1. [render.com](https://render.com) par sign up karein
2. **Dashboard** → **New +** → **PostgreSQL**
3. **Create Database** – Free plan select karein
4. **Internal Database URL** copy karein (e.g. `postgres://user:pass@host/db`)

## 3. APP_KEY generate karein

```bash
php artisan key:generate --show
```

Output copy karein (e.g. `base64:xxxxx...`)

## 4. Web Service create karein

1. **Dashboard** → **New +** → **Web Service**
2. Apna GitHub repo connect karein
3. Ye settings set karein:

| Field | Value |
|-------|-------|
| **Name** | school-management |
| **Region** | Singapore (ya apna nearest) |
| **Branch** | main |
| **Runtime** | **Docker** |
| **Build Command** | *(khali chhodo)* |
| **Start Command** | *(khali chhodo – Dockerfile use hoga)* |

## 5. Environment variables add karein

**Environment** section mein ye variables add karein:

| Key | Value |
|-----|-------|
| `APP_KEY` | `php artisan key:generate --show` ka output |
| `APP_URL` | `https://YOUR-SERVICE.onrender.com` (deploy ke baad update karein) |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `DB_CONNECTION` | `pgsql` |
| `DATABASE_URL` | *(PostgreSQL ka Internal URL – Render auto-add karega agar DB link kiya. Zaroori hai.)* |

Agar **PostgreSQL** ko Web Service se link kiya hai, toh `DATABASE_URL` auto add ho jata hai.

**Database link karne ke liye:**
- Web Service create karte waqt **Add Database** se PostgreSQL select karein
- Ya phir **Connections** tab mein existing database link karein

## 6. Deploy karein

**Create Web Service** par click karein. Build complete hone par app live ho jayegi.

## 7. Post-deploy: Seed data (optional)

Agar admin user aur demo data chahiye:

1. **Render Dashboard** → apna Web Service → **Shell** tab
2. Shell open karke run karein:
   ```
   php artisan db:seed --class=AdminSeeder
   php artisan db:seed --class=SchoolSeeder
   php artisan db:seed --class=DemoStudentsSeeder
   ```

3. **Admin login:** `admin@school.com` / `password`

## 8. APP_URL update karein

Deploy ke baad milne wala URL (e.g. `https://school-management-xxxx.onrender.com`) copy karke:

- Web Service → **Environment** → `APP_URL` = `https://YOUR-ACTUAL-URL.onrender.com`

## Important notes

- **Storage:** Uploaded files (photos) redeploy par delete ho jayenge – production ke liye S3/cloud storage use karein
- **15 min idle:** Free tier par 15 min inactivity ke baad app sleep ho jati hai; pehla request slow ho sakta hai
- **Database:** Free PostgreSQL 30 din ke baad expire hota hai – paid plan ya nayi DB zaroori hai
