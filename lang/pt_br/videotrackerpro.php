<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * videotrackerpro.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['analyticsheader'] = 'Analytics';
$string['completionpercent'] = 'Percentual mínimo assistido para conclusão';
$string['dropoff'] = 'Ponto de encerramento';
$string['ended'] = 'Fim';
$string['endreason'] = 'Motivo do encerramento da sessão';
$string['endreason:active_or_unclosed'] = 'Sessão ainda ativa ou sem sinal de encerramento';
$string['endreason:closed_before_end'] = 'A sessão foi encerrada antes do fim do vídeo';
$string['endreason:ended'] = 'O vídeo terminou normalmente';
$string['endreason:page_closed_or_navigated'] = 'A página foi fechada ou houve navegação antes do fim do vídeo';
$string['errorpercent'] = 'Informe um valor entre 0 e 100.';
$string['errortrackingrequired'] = 'A fonte selecionada no Video Bridge não fornece tracking confiável.';
$string['eventended'] = 'fim do vídeo';
$string['eventpause'] = 'pause';
$string['eventplay'] = 'play';
$string['eventplaying'] = 'reprodução retomada';
$string['eventrate'] = 'velocidade {$a->from}x → {$a->to}x';
$string['eventseek'] = 'seek {$a->from} → {$a->to} ({$a->direction})';
$string['eventsessionend'] = 'fim da sessão';
$string['eventsessionstart'] = 'início da sessão';
$string['eventvisibilitychange'] = 'visibilidade alterada';
$string['eventwaiting'] = 'buffering';
$string['lastpoint'] = 'Último ponto';
$string['lastview'] = 'Última visualização';
$string['myanalytics'] = 'Meus analytics';
$string['noevents'] = 'Nenhum evento ordenado foi registrado nesta sessão.';
$string['open'] = 'Aberta';
$string['pausedtime'] = 'Tempo pausado';
$string['pauses'] = 'Pausas';
$string['percent'] = 'Assistido';
$string['pluginadministration'] = 'Administração do Video Tracker Pro';
$string['pluginname'] = 'Video Tracker Pro';
$string['positions'] = 'Posição inicial → final';
$string['privacy:metadata'] = 'O Video Tracker Pro armazena apenas a configuração da atividade. O progresso e a telemetria ficam no local_video_bridge para este contexto.';
$string['privacy:metadata:bridge'] = 'O Video Bridge armazena progresso normalizado e telemetria compacta das sessões do Video Tracker Pro.';
$string['ratechanges'] = 'Mudanças de velocidade';
$string['reachedend'] = 'Evento ended recebido';
$string['recordbuffering'] = 'Registrar eventos de buffering';
$string['recorddropoff'] = 'Registrar encerramento antes do final';
$string['recordpauses'] = 'Registrar pausas';
$string['recordrates'] = 'Registrar mudanças de velocidade';
$string['recordseeks'] = 'Registrar seeks';
$string['recordsessions'] = 'Registrar sessões';
$string['report'] = 'Analytics de reprodução';
$string['seeks'] = 'Seeks';
$string['session'] = 'Sessão';
$string['sessionduration'] = 'Duração da sessão';
$string['sessionprogress'] = 'Progresso no início → fim da sessão';
$string['sessions'] = 'Sessões';
$string['sessiontimeline'] = 'Timeline da sessão';
$string['showpersonal'] = 'Mostrar analytics pessoais ao aluno';
$string['speedavg'] = 'Velocidade média';
$string['started'] = 'Início';
$string['videosource'] = 'Fonte de vídeo';
$string['videosourceheader'] = 'Fonte de vídeo';
$string['videotrackerpro:addinstance'] = 'Adicionar atividade Video Tracker Pro';
$string['videotrackerpro:view'] = 'Visualizar Video Tracker Pro';
$string['videotrackerpro:viewreport'] = 'Visualizar analytics de reprodução dos alunos';
$string['videotrackerproname'] = 'Nome';
$string['viewreport'] = 'Ver relatório de analytics';
$string['watchtime'] = 'Tempo ativo de reprodução';
