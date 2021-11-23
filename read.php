<?php
    require __DIR__ . "/vendor/autoload.php";
    use Source\Models\Serie;
    use Source\Models\Episodio;
    use Source\Models\Resenha;

    $serie = new Serie();
    $episodiosInfos = new Episodio();
    $resenha = new Resenha();
    $validar = $serie->find()->count();
    $quantidadeEps = $episodiosInfos->find()->count();

if ($validar) { // Verifica se tem registros no banco, se não tiver ele printa na tela que não há registros.
        $series = $serie->find()->fetch(true);
        $episodios = $episodiosInfos->find()->fetch(true);
        $resenhas = $resenha->find()->fetch(true);

        echo '<table style="width: 100%;" class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Capa</th>
                        <th scope="col">Título</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Ano Lançamento</th>
                        <th scope="col">Popularidade</th>
                        <th scope="col">Total episódios</th>
                        <th scope="col">Data do primeiro episódio</th>
                        <th scope="col">Gêneros</th>
                        <th scope="col">Avaliações</th>
                    </tr>
                    </thead>
                    <tbody>';

        foreach ($series as $serie) {
            foreach ($episodios as $episodio){
                $tmdb = $episodio->id_tmdb_serie;
                if ($tmdb == $serie->id_tmdb) {
                    $idEpisodio = $episodio->id;
                }
            }
            $infosEps = $episodiosInfos->findById($idEpisodio);

            echo '<tr id="' . $serie->id_tmdb . '">
                    <td style="width:10%; vertical-align: middle;"><img src=" http://image.tmdb.org/t/p/w92' . $serie->imagem . '" alt="Capa do filme"></td>
                    <td style="width:10%; font-weight: bold; text-align: left; vertical-align: middle;">' . $serie->nome . ' </td>
                    <td style="width:20%; text-align: center; vertical-align: middle;">' . $serie->descricao . '</td>
                    <td style="width:10%; text-align: center; vertical-align: middle;">' . $serie->ano_lancamento . '</td>
                    <td style="width:10%; text-align: center; vertical-align: middle;">' . $serie->popularidade . '</td>
                    <td style="width:10%; text-align: center; vertical-align: middle;">' . $infosEps->total_episodios . '</td>
                    <td style="width:10%; text-align: center; vertical-align: middle;">' . $infosEps->data_episodio_um . '</td>
                    <td style="width:10%; text-align: center; vertical-align: middle;">' . $infosEps->generos . '</td>';

            foreach ($resenhas as $resenhaInfo) {
                $tmdbResenha = $resenhaInfo->id_tmdb_serie;
                if ($tmdbResenha == $serie->id_tmdb) {
                    $idResenha = $resenhaInfo->id;
                    $infosResenhas = $resenha->findById($idResenha);
                    if ($infosResenhas) {
                        if ($infosResenhas->nota == NULL) {
                            $infosResenhas->nota = '-';
                        }
                        echo '<td class="col-xs-2" style="width:10%; text-align: center; vertical-align: middle;"><b>' . $infosResenhas->autor . '</b><br>' . $infosResenhas->texto_resenha . '<br><b>Nota:</b> ' . $infosResenhas->nota .  '<br></td>';
                    }
                }
            }
        }
        echo '</tr></tbody></table>';
    } else {
        echo '<h3>Não existem registros</h3>';
    }
