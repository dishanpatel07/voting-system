const { test, expect } = require('@playwright/test');

test.describe('Authentication Flows', () => {
  const uniqueEmail = `testuser_${Date.now()}@example.com`;
  const password = 'Password123!';

  test('User can register a new account', async ({ page }) => {
    await page.goto('register.php');
    
    // Fill out registration form
    await page.fill('input[name="name"]', 'Automated Test User');
    await page.fill('input[name="email"]', uniqueEmail);
    await page.fill('input[name="password"]', password);
    await page.fill('input[name="confirm_password"]', password);
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Verify successful registration message
    await expect(page.locator('.alert-success')).toContainText('Registration successful');
  });

  test('User can log in with registered account', async ({ page }) => {
    // Note: This relies on the previous test or we just create a new user manually
    // For isolation, we'll try to login. Wait, playwright runs tests in isolation!
    // So we need to register first inside this test or assume a seeded user.
    // Let's just test with a known user or seed it. 
    // Actually, let's just register and then login.
    
    const localEmail = `login_${Date.now()}@example.com`;
    
    // 1. Register
    await page.goto('register.php');
    await page.fill('input[name="name"]', 'Login Test User');
    await page.fill('input[name="email"]', localEmail);
    await page.fill('input[name="password"]', password);
    await page.fill('input[name="confirm_password"]', password);
    await page.click('button[type="submit"]');
    await expect(page.locator('.alert-success')).toContainText('Registration successful');
    
    // 2. Login
    await page.goto('login.php');
    await page.fill('input[name="email"]', localEmail);
    await page.fill('input[name="password"]', password);
    await page.click('button[type="submit"]');
    
    // 3. Verify Dashboard
    await expect(page).toHaveURL(/.*dashboard\.php/);
    await expect(page.locator('h2')).toContainText('Voter Dashboard');
  });

  test('Admin can log in with valid credentials', async ({ page }) => {
    await page.goto('admin/login.php');
    
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
    
    await expect(page).toHaveURL(/.*dashboard\.php/);
    await expect(page.locator('h2')).toContainText('Election Overview');
  });
});
