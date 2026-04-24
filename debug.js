const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  const response = await page.goto('http://127.0.0.1/online_voting/register.php');
  console.log('Status:', response.status());
  console.log('URL:', page.url());
  const html = await page.content();
  console.log('Contains input[name="name"]?', html.includes('name="name"'));
  console.log('HTML Snippet:', html.substring(0, 500));
  await browser.close();
})();
