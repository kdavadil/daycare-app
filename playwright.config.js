import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests/browser',
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 1 : 0,
  reporter: process.env.CI ? 'github' : 'list',
  use: { baseURL: 'http://127.0.0.1:8010', trace: 'retain-on-failure' },
  projects: [{ name: 'mobile-chromium', use: { ...devices['Pixel 7'] } }],
  webServer: {
    command: 'php artisan db:seed --force --no-interaction && php artisan serve --host=127.0.0.1 --port=8010 --no-reload',
    url: 'http://127.0.0.1:8010/up',
    reuseExistingServer: false,
    env: {
      APP_ENV: 'testing',
      SESSION_DRIVER: 'file',
      CACHE_STORE: 'array',
      MAIL_MAILER: 'array',
      DEMO_LOGIN_ENABLED: 'true',
      DEMO_LOGIN_PIN: 'playwright-demo',
    },
  },
});
