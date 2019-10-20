<?php
/**
 *	process szignálokat fogad és kezel
 *
 *	@author Zsombor
 *
 *	@example
 *

declare(ticks=1);
 
$sig = new sighandler( function($signo) {

    switch($signo){
        case SIGHUP: print('  SIGHUP kaptam   '); break;
        case SIGINT: print('  SIGINT kaptam   '); break;	// CTRL-C
        case SIGQUIT: print('  SIGQUIT kaptam   '); break;
        case SIGILL: print('  SIGILL kaptam   '); break;
        case SIGTRAP: print('  SIGTRAP kaptam   '); break;
        case SIGABRT: print('  SIGABRT kaptam   '); break;
        case SIGIOT: print('  SIGIOT kaptam   '); break;
        case SIGBUS: print('  SIGBUS kaptam   '); break;
        case SIGFPE: print('  SIGFPE kaptam   '); break;
        case SIGKILL: print('  SIGKILL kaptam   '); break;
        case SIGUSR1: print('  SIGUSR1 kaptam   '); break;
        case SIGSEGV: print('  SIGSEGV kaptam   '); break;
        case SIGUSR2: print('  SIGUSR2 kaptam   '); break;
        case SIGPIPE: print('  SIGPIPE kaptam   '); break;
        case SIGALRM: print('  SIGALRM kaptam   '); break;
        case SIGTERM: print('  SIGTERM kaptam   '); break;	// kill
        case SIGSTKFLT: print('  SIGSTKFLT kaptam   '); break;
        case SIGCLD: print('  SIGCLD kaptam   '); break;
        case SIGCHLD: print('  SIGCHLD kaptam   '); break;
        case SIGCONT: print('  SIGCONT kaptam   '); break;
        case SIGSTOP: print('  SIGSTOP kaptam   '); break;
        case SIGTSTP: print('  SIGTSTP kaptam   '); break;	// CTRL-Z
        case SIGTTIN: print('  SIGTTIN kaptam   '); break;
        case SIGTTOU: print('  SIGTTOU kaptam   '); break;
        case SIGURG: print('  SIGURG kaptam   '); break;
        case SIGXCPU: print('  SIGXCPU kaptam   '); break;
        case SIGXFSZ: print('  SIGXFSZ kaptam   '); break;
        case SIGVTALRM: print('  SIGVTALRM kaptam   '); break;
        case SIGPROF: print('  SIGPROF kaptam   '); break;
        case SIGWINCH: print('  SIGWINCH kaptam   '); break;
        case SIGPOLL: print('  SIGPOLL kaptam   '); break;
        case SIGIO: print('  SIGIO kaptam   '); break;
        case SIGPWR: print('  SIGPWR kaptam   '); break;
        case SIGSYS: print('  SIGSYS kaptam   '); break;
        case SIGBABY: print('  SIGBABY kaptam   '); break;
    }
}, [SIGINT,SIGTERM,SIGHUP,SIGQUIT,20] );


while( !$sig->signal() ) {
    sleep(2);
    pcntl_signal_dispatch();
}


 */


namespace zs;

declare(ticks=1);

/**
 *	szignálkezelő példánya
 */
class sighandler {
    
    /**
     *	szignálkezelőt hoz létre
     *
     *	@param $callable a metódus ami lefut ha kapjuk a szignált. Lehet SIG_DFL is, ha vissza akarjuk állítani az eredeti kezelőt.
     *	@param $signals a szignálok amit el kell kapni
     *	return void
    */
    function __construct($callable, $signals=[SIGTERM,SIGINT,SIGQUIT,SIGHUP] ){
        $this->_callable = $callable;
        foreach($signals as $signal)if(!in_array($signal,[9,19]))
            \pcntl_signal($signal, [$this,'handle'] );
    }
    private $_callable;
    
    
    /**
     *	visszaadja az utolsó kapott szignált
     */
    function signal(){
        \pcntl_signal_dispatch();
        return $this->_last_Signal;
    }
    private $_last_Signal=0;
    
    
    
    public function handle($signo){
        $this->_last_Signal = $signo;
        if(!is_numeric($callable = &$this->_callable))
            $callable($signo);
    }
    
    
}

