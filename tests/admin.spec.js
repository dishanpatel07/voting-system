const { test, expect } = require('@playwright/test');

test.describe('Admin Workflows', () => {
  
  test.beforeEach(async ({ page }) => {
    // Log in as admin before each test in this block
    await page.goto('admin/login.php');
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/.*dashboard\.php/);
  });

  test('Admin can add a new candidate', async ({ page }) => {
    const candidateName = `Automated Candidate ${Date.now()}`;
    const partyName = 'Test Party';

    await page.goto('admin/add_candidate.php');
    
    await page.fill('input[name="name"]', candidateName);
    await page.fill('input[name="party"]', partyName);
    await page.click('button[type="submit"]');

    // Should redirect to dashboard and show success message
    await expect(page).toHaveURL(/.*dashboard\.php/);
    await expect(page.locator('.alert-success')).toContainText('Candidate added successfully');
    
    // Verify candidate is in the table
    await expect(page.locator('table')).toContainText(candidateName);
  });

  test('Admin can delete a candidate', async ({ page }) => {
    // First, add a specific candidate to delete
    const candidateName = `Delete Me ${Date.now()}`;
    await page.goto('admin/add_candidate.php');
    await page.fill('input[name="name"]', candidateName);
    await page.fill('input[name="party"]', 'Delete Party');
    await page.click('button[type="submit"]');

    // Make sure it's in the table
    await expect(page.locator('table')).toContainText(candidateName);

    // Find the row with this candidate and click its delete button
    const row = page.locator('tr', { hasText: candidateName });
    
    // Set up a dialog handler to automatically accept the confirmation alert
    page.on('dialog', dialog => dialog.accept());
    
    await row.locator('button:has-text("Delete")').click();

    // Verify successful deletion
    await expect(page.locator('.alert-success')).toContainText('Candidate deleted successfully');
    await expect(page.locator('table')).not.toContainText(candidateName);
  });
});
