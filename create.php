<?php

    require __DIR__ . "/vendor/autoload.php";
    use Source\Models\Serie;
    use Source\Models\Episodio;
    use Source\Models\Resenha;

    $numeroSeries = 0;
    $pagina = 1;
    $client = new GuzzleHttp\Client();

    $confereEpidosodios = new Episodio();
    $contador = new Serie();
    $validar = $contador->find()->count();

    $today = date('Y-m-d');

    // Valida se já tem dados cadastrados no banco.
    if ($validar == 100) {
        $series = $contador->find()->fetch(true);

        foreach ($series as $serie) {
            $idReferencia = $serie->id;
        }

        $registro = $contador->findById($idReferencia);
        $dataCriacao = substr($registro->created_at, 0, 10);

        // Como o TMDB é atualizado uma vez por dia, fiz essa validação para atualizar apenas uma vez por dia (nesse caso quando o usuário acessar e o dia tiver mudado.)
        if ($dataCriacao == $today){
            echo "<h4>Banco atualizado!</h4>";
        } else { // Caso não esteja atualizado, ele exclui os dados e cadastra tudo novamente.
            for($i = $idReferencia-99;$i <= $idReferencia; $i++) {
                $registro = $contador->findById($i);
                $registro->destroy();
            }
            while ($numeroSeries < 100) {
                $resposta = $client->request('GET', 'https://api.themoviedb.org/3/tv/popular?api_key=210bc19a40819b6c46b376ba36ecb9c8&language=pt-BR&page=' . $pagina);

                $dados = json_decode($resposta->getBody(), false);

                foreach ($dados->results as $serie) {
                    $nome = $serie->name;
                    $descricao = $serie->overview;
                    $anoLancamento = $serie->first_air_date;
                    $popularidade = $serie->popularity;
                    $imagem = $serie->poster_path;
                    $idTMDB = $serie->id;

                    $serieDB = new Serie();

                    $serieDB->nome = $nome;
                    $serieDB->descricao = $descricao;
                    $serieDB->imagem = $imagem;
                    $serieDB->popularidade = $popularidade;
                    $serieDB->ano_lancamento = $anoLancamento;
                    $serieDB->id_tmdb = $idTMDB;


                    $serieDB->save();
                    $numeroSeries++;

                }
                $pagina++;
            }

            foreach ($series as $serie) {
                $idTMDBSerie = $serie->id_tmdb;

                $respostaSegundaAPI = $client->request('GET', 'https://api.themoviedb.org/3/tv/' . $idTMDBSerie . '?api_key=210bc19a40819b6c46b376ba36ecb9c8&language=pt-BR');

                $dadosSegundaAPI = json_decode($respostaSegundaAPI->getBody(), false);

                $episodiosInfos = new Episodio();

                $generos = '';
                for ($genero = 0; $genero < count($dadosSegundaAPI->genres); $genero++) {
                    if ($genero != count($dadosSegundaAPI->genres)-1){
                        $generos = $generos . $dadosSegundaAPI->genres[$genero]->name . ", ";
                    } else {
                        $generos = $generos . $dadosSegundaAPI->genres[$genero]->name . ".";
                    }
                }

                $episodiosInfos->id_tmdb_serie = $idTMDBSerie;
                $episodiosInfos->total_episodios = $dadosSegundaAPI->number_of_episodes;
                $episodiosInfos->data_episodio_um = $dadosSegundaAPI->first_air_date;
                $episodiosInfos->generos = $generos;

                $episodiosInfos->save();
            }


            foreach ($series as $serie) {
                $idTMDBSerie = $serie->id_tmdb;

                $respostaAPIAvaliacoes = $client->request('GET', 'https://api.themoviedb.org/3/tv/' . $idTMDBSerie . '/reviews?api_key=210bc19a40819b6c46b376ba36ecb9c8&language=pt-BR&page=1');

                $dadosAPIAvaliacoes = json_decode($respostaAPIAvaliacoes->getBody(), false);

               foreach ($dadosAPIAvaliacoes->results as $resenha) {
                   $resenhas = new Resenha();

                   $resenhas->id_tmdb_serie = $idTMDBSerie;
                   $resenhas->autor = $resenha->author;
                   $resenhas->texto_resenha = $resenha->content;
                   $resenhas->nota = $resenha->author_details->rating;

                   $resenhas->save();
               }
            }
        }
    } else { // Aqui ele faz o cadastro caso não tenha nenhum registro no banco de dados.
        while ($numeroSeries < 100) { // primeira API
            $resposta = $client->request('GET', 'https://api.themoviedb.org/3/tv/popular?api_key=210bc19a40819b6c46b376ba36ecb9c8&language=pt-BR&page=' . $pagina);

            $dados = json_decode($resposta->getBody(), false);

            foreach ($dados->results as $serie) {
                $nome = $serie->name;
                $descricao = $serie->overview;
                $anoLancamento = $serie->first_air_date;
                $popularidade = $serie->popularity;
                $imagem = $serie->poster_path;
                $idTMDB = $serie->id;

                $serieDB = new Serie();

                $serieDB->nome = $nome;
                $serieDB->descricao = $descricao;
                $serieDB->imagem = $imagem;
                $serieDB->popularidade = $popularidade;
                $serieDB->ano_lancamento = $anoLancamento;
                $serieDB->id_tmdb = $idTMDB;


                $serieDB->save();
                $numeroSeries++;

            }
            $pagina++;
        }

        $validaSeries = new Serie();

        $series = $validaSeries->find()->fetch(true);

        foreach ($series as $serie) { // Segunda API
            $idTMDBSerie = $serie->id_tmdb;

            $respostaSegundaAPI = $client->request('GET', 'https://api.themoviedb.org/3/tv/' . $idTMDBSerie . '?api_key=210bc19a40819b6c46b376ba36ecb9c8&language=pt-BR');

            $dadosSegundaAPI = json_decode($respostaSegundaAPI->getBody(), false);

            $episodiosInfos = new Episodio();

            $generos = '';
            for ($genero = 0; $genero < count($dadosSegundaAPI->genres); $genero++) {
                if ($genero != count($dadosSegundaAPI->genres)-1){
                    $generos = $generos . $dadosSegundaAPI->genres[$genero]->name . ", ";
                } else {
                    $generos = $generos . $dadosSegundaAPI->genres[$genero]->name . ".";
                }
            }

            $episodiosInfos->id_tmdb_serie = $idTMDBSerie;
            $episodiosInfos->total_episodios = $dadosSegundaAPI->number_of_episodes;
            $episodiosInfos->data_episodio_um = $dadosSegundaAPI->first_air_date;
            $episodiosInfos->generos = $generos;

            $episodiosInfos->save();
        }

        foreach ($series as $serie) { // Terceira API
            $idTMDBSerie = $serie->id_tmdb;

            $respostaAPIAvaliacoes = $client->request('GET', 'https://api.themoviedb.org/3/tv/' . $idTMDBSerie . '/reviews?api_key=210bc19a40819b6c46b376ba36ecb9c8&language=pt-BR&page=1');

            $dadosAPIAvaliacoes = json_decode($respostaAPIAvaliacoes->getBody(), false);

            foreach ($dadosAPIAvaliacoes->results as $resenha) {
                $resenhas = new Resenha();

                $resenhas->id_tmdb_serie = $idTMDBSerie;
                $resenhas->autor = $resenha->author;
                $resenhas->texto_resenha = $resenha->content;
                $resenhas->nota = $resenha->author_details->rating;

                $resenhas->save();
            }
        }
    }





