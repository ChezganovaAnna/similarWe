const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();

    // Список пользователей
    const users = [
        '211043234',
        '36419',
        '40259',
        '48205',
        '50009',
        // Добавьте здесь остальных пользователей
    ];

    // Функция для вызова скрипта для каждого пользователя
    async function processUser(user) {
        try {
            // URL для вызова скрипта
            const url = `https://vk.com/audios${user}`;

            // Вызов скрипта
            await page.goto(url, { waitUntil: 'networkidle0' });

            // Вызов скрипта
            await (async () => {
                const scroll = (top) => page.evaluate((top) => window.scrollTo({ top }));
                const delay = (ms) => new Promise((r) => setTimeout(r, ms));

                async function scrollPlaylist() {
                    const spinner = await page.$('.CatalogBlock__autoListLoader');
                    let pageHeight = 0;
                    do {
                        pageHeight = await page.evaluate(() => document.body.clientHeight);
                        scroll(pageHeight);
                        await delay(400);
                    } while (
                        pageHeight < await page.evaluate(() => document.body.clientHeight) ||
                        spinner?.style.display === ''
                        );
                }

                async function parsePlaylist() {
                    return await page.$$eval('.audio_row__performer_title', (rows) =>
                        rows.map((row) => {
                            const [artist, title] = [
                                '.audio_row__performers',
                                '.audio_row__title',
                            ].map((selector) => row.querySelector(selector)?.textContent || '');
                            return [artist, title].join(' - ');
                        }),
                    );
                }

                function saveToFile(filename, content) {
                    const data = content.replace(/\n/g, '\r\n');
                    const blob = new Blob([data], { type: 'text/plain' });
                    const link = document.createElement('a');
                    link.download = filename;
                    link.href = URL.createObjectURL(blob);
                    link.target = '_blank';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

                // Вызов скрипта
                await scrollPlaylist();
                const list = await parsePlaylist();
                saveToFile(`vk-playlist-${user}.txt`, list.join('\n'));
            })();
        } catch (error) {
            console.error(`Error processing user ${user}: ${error.message}`);
        }
    }

    // Вызов функции для каждого пользователя
    await Promise.all(users.map(processUser));

    await browser.close();
})();