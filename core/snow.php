<?php
// for normal snow basically without header lol
?>
<link rel="stylesheet" href="/assets/css/snow.css">
<script>
function createSnowflake() {
    const snowflake = document.createElement("div");
    snowflake.classList.add("snowflake");
    snowflake.textContent = "❄";

    snowflake.style.fontSize = (Math.random() * 10 + 10) + "px";

    snowflake.style.left = Math.random() * window.innerWidth + "px";

    snowflake.style.animationDuration = (Math.random() * 3 + 3) + "s";

    document.body.appendChild(snowflake);

    setTimeout(() => snowflake.remove(), 6000);
}

setInterval(createSnowflake, 150);
</script>
