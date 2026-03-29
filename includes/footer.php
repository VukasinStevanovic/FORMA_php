<?php
?>
<footer class="footer">
    <div class="footer-inner">
        <div>
            <h4>🏋️ Forma Fitness Gym</h4>
            <p>Dobrodošli u Forma Fitness — mesto gde se postavljaju i dostižu ciljevi.
               Moderna oprema, iskusni treneri i povoljna cena.</p>
            <p style="margin-top:10px;">
                📍 Vladimira Žestića 29, Pančevo<br>
                📞 +381 66 5 771171<br>
                ✉️ vukasin.stevanovic.34.24@ict.edu.rs
            </p>
        </div>
        <div>
            <h4>Brzi linkovi</h4>
            <ul>
                <?php
                global $meni;
                if (!empty($meni)):
                    foreach ($meni as $stavka): ?>
                        <li><a href="<?= e($stavka['url']) ?>"><?= e($stavka['naziv']) ?></a></li>
                    <?php endforeach;
                endif; ?>
            </ul>
        </div>
        <div>
            <h4>Radno vreme</h4>
            <ul>
                <li>Pon – Pet: 09:00 – 23:00</li>
                <li>Subota: 09:00 – 22:00</li>
                <li>Nedelja: zatvoreno</li>
            </ul>
            <p style="margin-top:14px;">
                <a href="<?= BASE_URL ?>/anketa.php">📊 Glasaj u anketi</a><br>
                <a href="<?= BASE_URL ?>/o_autoru.php" style="margin-top:4px;display:inline-block;">ℹ️ O autoru</a>
            </p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Forma Fitness Gym. Studentski projekat — sva prava zadržana.</p>
    </div>
</footer>
</body>
</html>
