import { test, expect } from '@playwright/test';

test('mobile foundation performs a Livewire request without overflow', async ({ page }) => {
  const errors = [];
  page.on('pageerror', error => errors.push(error.message));
  await page.goto('/');
  await expect(page.getByRole('heading', { name: 'A little space to grow.' })).toBeVisible();
  await page.getByRole('link', { name: 'Sign in' }).click();
  await expect(page.getByRole('heading', { name: 'Welcome back to Sibol.' })).toBeVisible();
  await page.goto('/');
  await expect(page.getByRole('link', { name: /Roster/ })).toBeVisible();
  await page.getByRole('link', { name: /Roster/ }).click();
  await expect(page.getByRole('heading', { name: 'Little Seeds Preschool' })).toBeVisible();
  await expect(page.getByText('Teacher Ana Cruz')).toBeVisible();
  await page.getByRole('link', { name: 'Open attendance' }).click();
  await expect(page.getByRole('heading', { name: 'Check children in and out' })).toBeVisible();
  await expect(page.getByText('Maya Dela Cruz')).toBeVisible();
  await page.getByRole('link', { name: /Sibol/ }).click();
  await page.getByRole('button', { name: 'Change language' }).click();
  await expect(page.getByRole('heading', { name: 'Munting espasyo para lumago.' })).toBeVisible();
  await page.setViewportSize({ width: 320, height: 740 });
  expect(await page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth)).toBeTruthy();
  expect(errors).toEqual([]);
});
