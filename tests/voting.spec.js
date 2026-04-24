const { test, expect } = require('@playwright/test');

test.describe('Voting Flow', () => {

  test('User can cast a vote and is then prevented from voting again', async ({ page }) => {
    // 1. Admin setup: create a candidate to vote for
    await page.goto('admin/login.php');
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/.*dashboard\.php/);
    
    const candidateName = `Voting Target ${Date.now()}`;
    await page.goto('admin/add_candidate.php');
    await page.fill('input[name="name"]', candidateName);
    await page.fill('input[name="party"]', 'Target Party');
    await page.click('button[type="submit"]');
    await expect(page.locator('.alert-success')).toContainText('Candidate added successfully');
    await page.goto('admin/logout.php');

    // 2. Register a new user
    const voterEmail = `voter_${Date.now()}@example.com`;
    const password = 'VotePassword1!';
    
    await page.goto('register.php');
    await page.fill('input[name="name"]', 'Honest Voter');
    await page.fill('input[name="email"]', voterEmail);
    await page.fill('input[name="password"]', password);
    await page.fill('input[name="confirm_password"]', password);
    await page.click('button[type="submit"]');
    await expect(page.locator('.alert-success')).toContainText('Registration successful');

    // 3. Login as new user
    await page.goto('login.php');
    await page.fill('input[name="email"]', voterEmail);
    await page.fill('input[name="password"]', password);
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/.*dashboard\.php/);

    // 4. Verify candidate is on the page
    await expect(page.locator('.candidates-grid')).toContainText(candidateName);

    // 5. Cast a vote for the candidate
    const candidateCard = page.locator('.glass-panel', { hasText: candidateName });
    await candidateCard.locator('button:has-text("Vote for")').click();

    // 6. Verify successful vote
    await expect(page).toHaveURL(/.*dashboard\.php/);
    await expect(page.locator('.alert-success')).toContainText('Your vote has been cast successfully!');

    // 7. Verify vote buttons are no longer present anywhere on the page
    const voteButtons = page.locator('button:has-text("Vote for")');
    await expect(voteButtons).toHaveCount(0);
    
    // Verify "You have already cast your vote" message is present
    await expect(page.locator('.alert-success').first()).toContainText('You have already cast your vote');
  });

});
