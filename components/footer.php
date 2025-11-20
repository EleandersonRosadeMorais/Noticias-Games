    </main>
    <footer class="rodape">
        <div class="info-container">
            <?php
            $url_dolar = "https://economia.awesomeapi.com.br/last/USD-BRL";
            $json_dolar = @file_get_contents($url_dolar);
            
            if ($json_dolar != false) {
                $dados_dolar = json_decode($json_dolar, true);
                if (isset($dados_dolar["USDBRL"]["bid"])) {
                    
                    echo "<span class='info-item'>💵 Dólar: R$ " . number_format($dados_dolar["USDBRL"]["bid"], 2, ',', '.') . "</span>";
                }
            }
            ?>
            
            <span id="temperatura-container" class="info-item">
                🌡️ Carregando...
            </span>
        </div>
        <span>🎮 &copy; <?php echo date('Y'); ?> NoticiasGames - Todos os direitos reservados</span>
    </footer>

    <script>
    function carregarTemperatura(latitude, longitude) {
   
        fetch(`https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current=temperature_2m`)
            .then(response => response.json())
            .then(data => {
                if (data.current && data.current.temperature_2m) {
                    document.getElementById('temperatura-container').innerHTML = 
                        `🌡️ Temperatura: ${data.current.temperature_2m}°C`;
                } else {
                    document.getElementById('temperatura-container').innerHTML = 
                        '🌡️ Temperatura indisponível';
                }
            })
            .catch(error => {
                document.getElementById('temperatura-container').innerHTML = 
                    '🌡️ Erro ao carregar';
            });
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                carregarTemperatura(
                    position.coords.latitude, 
                    position.coords.longitude
                );
            },
            function(error) {

                document.getElementById('temperatura-container').innerHTML = 
                    '🌡️ Clique aqui para ativar localização';
                
                document.getElementById('temperatura-container').onclick = function() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                carregarTemperatura(
                                    position.coords.latitude, 
                                    position.coords.longitude
                                );
                            }
                        );
                    }
                };
            }
        );
    } else {
        document.getElementById('temperatura-container').innerHTML = 
            '🌡️ Geolocalização não suportada';
    }
    </script>
</body>
</html>