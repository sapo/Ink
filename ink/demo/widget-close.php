<?php include 'shared/header.php'; ?>



<style type="text/css">
</style>



<div class="ink-container">
    <div class="ink-section">
        <div class="ink-l30">
            <div class="ink-space">
                <h3>Alertas Básicos</h3>
                <p>Os alertas básicos são úteis para formulários ou notificações simples.</p>
                <p>Para usar o estilo dos alertas básicos basta usar a class <code>.alert-msg</code>, podendo esta ser acompanhada das classes:</p>
                <ul class="unstyled">
                    <li><code>.error</code> - Para Mensagens de erro</li>
                    <li><code>.success</code> - Para Mensagens de successo</li>
                    <li><code>.info</code> - Para Mensagens Informativas</li>
                </ul>
            </div>
        </div> 
        <div class="ink-l70">
            <div class="ink-space">
                <div class="alert-msg">
                    <button class="close">×</button>
                    <p><b>Aviso:</b> Aqui fica o texto da notificação</p>
                </div>

                <div class="alert-msg error">
                    <button class="close">×</button>
                    <p><b>Erro:</b> Mensagem de Erro</p>
                </div>

                <div class="alert-msg success">
                    <button class="close">×</button>
                    <p><b>Concluído:</b> Mensagem de successo</p>
                </div>

                <div class="alert-msg info">
                    <button class="close">×</button>
                    <p><b>Nota:</b> Mensagem de Informação</p>
                </div>
                <pre class="prettyprint"><ol><li><span class="tag">&lt;div</span><span class="tag"> <span class="pln"></span><span class="atn">class</span><span class="pun">=</span><span class="atv">"alert-msg"</span>&gt;</span></li><li>  <span class="tag">&lt;button</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"close"</span><span class="tag">&gt;</span><span class="pln">x</span><span class="tag">&lt;/button&gt;</span></li><li>  <span class="tag">&lt;p&gt;</span><span class="pln">Texto da mensagem</span><span class="tag">&lt;/p&gt;</span><span class="tag"></span></li><li><span class="tag">&lt;/div&gt;</span><span class="tag"></span></li></ol></pre>
            </div>
        </div>
    </div>
    <div class="ink-section">
        <div class="ink-l30">
            <div class="ink-space">
                <h3>Alertas em Bloco</h3>
                <p>Os alertas em bloco são úteis para formulários com explicação detalhada de erros, ou erros onde é necessário acção.</p>
                <ul class="unstyled">
                    <li><code>.error</code> - Para Mensagens de erro</li>
                    <li><code>.success</code> - Para Mensagens de successo</li>
                    <li><code>.info</code> - Para Mensagens Informativas</li>
                </ul>
            </div>
        </div>
        <div class="ink-l70">
            <div class="ink-space">
                <div class="block-alert-msg">
                    <button class="close">×</button>
                    <h4>Isto é uma notificação</h4>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    </p>
                </div>

                <div class="block-alert-msg error">
                    <button class="close">×</button>
                    <h4>Ocorreram os seguintes erros:</h4>
                    <ul>
                        <li>Nome de Utilizador Inválido</li>
                        <li>Email Inválido</li>
                    </ul>
                </div>

                <div class="block-alert-msg success">
                    <button class="close">×</button>
                    <h4>Processo Concluído</h4>
                    <p>O seu formulário foi submetido e aguarda revisão.</p>
                </div>
                <pre class="prettyprint"><ol><li><span class="tag">&lt;div</span><span class="tag"> <span class="pln"></span><span class="atn">class</span><span class="pun">=</span><span class="atv">"block-alert-msg error"</span>&gt;</span></li><li><span class="tag">   &lt;button</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"close"</span><span class="tag">&gt;</span><span class="pln">x</span><span class="tag">&lt;/button&gt;</span></li><li><span class="tag">   &lt;h4</span><span class="pln"></span><span class="atv"></span><span class="tag">&gt;</span><span class="pln">Ocorreram os seguintes erros</span><span class="tag">&lt;/h4&gt;</span></li><li>   <span class="tag">&lt;ul</span><span class="tag">&gt;</span></li><li>      <span class="tag">&lt;li&gt;</span><span class="pln">Nome de utilizador inválido</span><span class="tag">&lt;/li&gt;</span><span class="tag"></span></li><li><span class="tag">      &lt;li&gt;</span><span class="pln">Nome de utilizador inválido</span><span class="tag">&lt;/li&gt;</span><span class="tag"></span></li><li><span class="tag">   &lt;/ul</span><span class="pln"></span><span class="tag">&gt;</span><span class="tag"></span><span class="tag"></span></li><li><span class="tag">&lt;/div&gt;</span><span class="tag"></span></li></ol></pre>
            </div>
        </div>
        
    </div>
</div>



<div class="ink-space">
    <div class="block-alert-msg">
        <button class="close">×</button>
        <h4>Isto é uma notificação</h4>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
        </p>
    </div>

    <div class="block-alert-msg error">
        <button class="close">×</button>
        <h4>Ocorreram os seguintes erros:</h4>
        <ul>
            <li>Nome de Utilizador Inválido</li>
            <li>Email Inválido</li>
        </ul>
    </div>

    <div class="block-alert-msg success">
        <button class="close">×</button>
        <h4>Processo Concluído</h4>
        <p>O seu formulário foi submetido e aguarda revisão.</p>
    </div>
    <pre class="prettyprint"><ol><li><span class="tag">&lt;div</span><span class="tag"> <span class="pln"></span><span class="atn">class</span><span class="pun">=</span><span class="atv">"block-alert-msg error"</span>&gt;</span></li><li><span class="tag">   &lt;button</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"close"</span><span class="tag">&gt;</span><span class="pln">x</span><span class="tag">&lt;/button&gt;</span></li><li><span class="tag">   &lt;h4</span><span class="pln"></span><span class="atv"></span><span class="tag">&gt;</span><span class="pln">Ocorreram os seguintes erros</span><span class="tag">&lt;/h4&gt;</span></li><li>   <span class="tag">&lt;ul</span><span class="tag">&gt;</span></li><li>      <span class="tag">&lt;li&gt;</span><span class="pln">Nome de utilizador inválido</span><span class="tag">&lt;/li&gt;</span><span class="tag"></span></li><li><span class="tag">      &lt;li&gt;</span><span class="pln">Nome de utilizador inválido</span><span class="tag">&lt;/li&gt;</span><span class="tag"></span></li><li><span class="tag">   &lt;/ul</span><span class="pln"></span><span class="tag">&gt;</span><span class="tag"></span><span class="tag"></span></li><li><span class="tag">&lt;/div&gt;</span><span class="tag"></span></li></ol></pre>
</div>


<script type="text/javascript">
    SAPO.Ink.Close();
</script>
