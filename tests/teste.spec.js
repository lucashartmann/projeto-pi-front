import { test, expect } from '@playwright/test';

test('Deve carregar a página inicial', async ({ page }) => {
  await page.goto('http://127.0.0'); 
  
  const titulo = page.locator('h1');
  await expect(titulo).toBeVisible();
});
