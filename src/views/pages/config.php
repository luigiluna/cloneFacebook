<?=$render('header', ['loggedUser'=> $loggedUser]);?>

<section class="container main">
    <?=$render('sidebar', ['activeMenu'=>'search']);?>
    <section class="feed">

    <form method="POST" action="<?=$base;?>/config" enctype="multipart/form-data">


        <?php if(!empty($flash)): ?>
            <div class="flash"><?= $flash; ?></div>
        <?php endif;?>

        </br>
        Avatar:
        </br>
        <input type="file" name="avatar"/> 
        </br>
        </br>
        Cover:
        </br>
        <input type="file" name="cover"/> 

        <hr>

        </br>
        Nome completo:
        </br>
        <input placeholder="Campo obrigatório"  type="text" name="name" class="input-config" value="<?= ($user->name) ? $user->name : ' ' ?>"/>
        </br>

        </br>
        Data de nascimento:
        </br>
        <input placeholder="Campo obrigatório"  type="text" name="birthDate" id="birthDate" class="input-config" value="<?= ($user->birthDate) ? $user->birthDate : ' ' ?>"/>
        </br>

        </br>
        E-mail:
        </br>
        <input placeholder="Campo obrigatório"  type="email" name="email" class="input-config" value="<?= ($user->email) ? $user->email : ' ' ?>"/>
        </br>

        </br>
        Cidade:
        </br>
        <input placeholder="Cidade"  type="text" name="city" class="input-config" value="<?= ($user->city) ? $user->city : ' ' ?>"/>
        </br>

        </br>
        Trabalho:
        </br>
        <input placeholder="Trabalho"  type="text" name="work" class="input-config" value="<?= ($user->work) ? $user->work : ' ' ?>"/>
        </br>

        <hr>

        </br>
        Nova Senha:
        </br>
        <input placeholder="Nova Senha"  type="password" name="newPassword" class="input-config"/>
        </br>

        </br>
       Confirmar Nova Senha:
        </br>
        <input placeholder="Nova Senha"  type="password" name="confirmNewPassword" class="input-config"/>
        </br>

        <input class="button" type="submit" value="Salvar" />

    </form>
   
</section>

<script src="https://unpkg.com/imask"></script>
<script> 
IMask(
    document.getElementById('birthDate'),
    {
        mask:'00/00/0000'
    }
);
</script>
<?=$render('footer');?>