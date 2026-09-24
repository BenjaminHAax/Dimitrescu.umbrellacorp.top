const { setTimeout: sleep } = require("node:timers/promises");

// Test async function using only await
async function testAsync() {
    await sleep(1000);
    return "Async function resolved";
}

async function main() {
    console.log("Waiting for async function...");
    const result = await testAsync();
    console.log(result);
}

main();