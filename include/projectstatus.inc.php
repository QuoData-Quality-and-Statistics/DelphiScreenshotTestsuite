<?php

require_once '../include/queue.inc.php';

function startProjectTest($sProject, $sCmd) {
    global $sAhkCmd, $sAhkFolderPl;
    if (!empty($sCmd)) {
        $sCheckRunningTestsScript = $sAhkFolderPl . '/auf laufende Tests pruefen.ahk';
        if (file_exists($sCheckRunningTestsScript))
            $sCmd = "$sAhkCmd \"$sCheckRunningTestsScript\" && $sCmd";
        exec("( $sCmd ) 2>&1", $aOutput, $iStatus);
        $sOutput = join("\n", $aOutput);
        $sColor = $iStatus ? 'red' : 'green';
        $_GET['message'] = "Kommandozeile '$sCmd' wurde ausgef&uuml;hrt " .
                "(Rückgabewert $iStatus). " .
                "<pre style='color:$sColor'>$sOutput</pre>";

        // Create directory (current design) and LOCK-File (running process)
        $sRunningProcessFolderPl = dirname(__FILE__) . '/../html/RunningProcess/';
        $sFileName = $sRunningProcessFolderPl . $sProject . '.LOCK';

        if (!file_exists($sRunningProcessFolderPl)) {
            mkdir($sRunningProcessFolderPl, 0777, true);
        }
        if ($iStatus === 0) {
            file_put_contents($sFileName, '');
        }
    }
    else {
        $_GET['message'] = 'Fuer dieses Projekt wurde keine Kommandozeile hinterlegt.';
    }
}

/**
 * @param string $p_sExePath wird für Ermittlung des Verfallsdatums verwendet, wird nicht zum Start verwendet
 */
function getProjectStatus($sProject, $p_sExePath, $sCmd = '') {
    global $sExePath, $iExeTime, $aTests, $aProjects, $iStatusSum, $iLocalStatusSum, $aNewTests, $sDoneFile;

    if (!empty($_GET['project']) && $_GET['project'] != $sProject)
        return;

    if (!empty($_GET['run'])) {
        startProjectTest($sProject, $sCmd);
    }

    $sDoneFile = "C:/xampp/htdocs/DelphiScreenshotTestsuite/html/FinishedProcess/$sProject.DONE";

    $sPicturePath = "$sProject/";
    $sExePath = $p_sExePath;
    $iExeTime = filemtime($sExePath);
    $aNewTests = array();
    $iLocalStatusSum = 0;
    array_walk(glob("Bilder/$sPicturePath*-ist.???"), function($sFile) {
        global $aNewTests, $iLocalStatusSum, $sScreenshotName;

        $aNewTests[] = $aTest = getScreenshotStatus($sFile);
        $iLocalStatusSum += $aTest['status'];
        if ($aTest['ext'] == 'pdf' || $aTest['ext'] == 'bmp') {
            global $bNeedsFurtherConversions;
            if (empty($bNeedsFurtherConversions))
                $bNeedsFurtherConversions = 1;
            else
                $bNeedsFurtherConversions++;

            $sScreenshotName = $aTest['name'];
        }
    });

    $aProjects[] = array(
        'title' => $sProject,
        'status' => $iLocalStatusSum == count($aNewTests) ? 1 : 0,
        'ratio' => $iLocalStatusSum . " / " . count($aNewTests),
        'cmd' => $sCmd
    );
    $aTests = array_merge($aTests, $aNewTests);
    $iStatusSum += $iLocalStatusSum;
}

$sAhkCmd = '"C:\Program Files\AutoHotkey\AutoHotkey.exe" /ErrorStdOut';
$sAhkFolderPl = getenv('USERPROFILE') . '\Desktop\ScreenshotsPROLab';

function getProjectStatusPl($sProject, $sExePath) {
    global $sAhkCmd, $sAhkFolderPl;
    $sAhkScriptFile = $sAhkFolderPl.'\\'."Test starten - $sProject.ahk";
    if (file_exists($sAhkScriptFile))
        getProjectStatus($sProject, $sExePath, "$sAhkCmd \"$sAhkScriptFile\"");
}

function getStatusOfAllProjects() {
    global $aTests;
    $aTests = array();
    $sHost = strtolower(gethostname());
    if (in_array($sHost, array('screenshot01-pc'))) {
        getProjectStatusPl('PROLab_de', 'c:/daten/prolab_plus_de_AD\\PROLab_de.exe');
        getProjectStatusPl('PROLab_en', 'c:/daten/prolab_plus_en_AD\\PROLab_en.exe');
        getProjectStatusPl('PROLab_fr', 'c:/daten/prolab_plus_fr_AD\\PROLab_fr.exe');
        getProjectStatusPl('PROLab_es', 'c:/daten/prolab_plus_es_AD\\PROLab_es.exe');

        getProjectStatusPl('PROLab_Torte', 'c:/daten/prolab_Torte\\PROLab_de.exe');
        getProjectStatusPl('PROLab_RVTypKurz', 'c:/daten/prolab_RVTypKurz\\PROLab_de.exe');

        getProjectStatusPl('mqVAL_DE', 'c:/daten/mqVAL_DE\\mqVAL.exe');
        getProjectStatusPl('PROLab_Smart_DE', 'c:/daten/PROLab_Smart_DE_13528\\PROLabSmart.exe');
        getProjectStatusPl('RingDat_en', 'c:/daten/RingDat_EN\\RingDat4_en.exe');
        getProjectStatusPl('RingDat_de', 'c:/daten/RingDat_DE\\RingDat4_de.exe');
        getProjectStatusPl('PROLab_POD_EN', 'c:/daten/PROLab_POD_EN\\PROLabSmart.exe');
        getProjectStatusPl('PROLab_D2010', 'c:/daten/prolab_D2010\\PROLab_D2010.exe');
        getProjectStatusPl('CalcInterface_LPP', 'c:/daten/CalcInterface_LPP\\CalcInterface.exe');

        // InterVAL soll im Moment nicht in die Jobliste gespeichert werden, da noch kein job_done Parameter von InterVAL übergeben wird.
        getProjectStatus('InterVAL', 'c:/daten/InterVAL\\InterVAL.exe', "C:\\Daten\\InterVAL\\InterVAL.exe /create_test_images C:\\xampp\\htdocs\\DelphiScreenshotTestsuite\\html\\Bilder\\InterVAL");
    }
    if (in_array($sHost, array(
                'screenshot02-pc', 'screenshot01-pc',
                'noack-pc',
                'rot2-pc')) || strstr($sHost, 'blaeul')) {
        $sLvuTestPath = 'C:\xampp\htdocs\lvu\tests\PhantomJS';
        $sLvuGitRef = $sLvuTestPath.'\Alter_des_Branches-reviewed-code-for-screenshots.txt';
        getProjectStatus('RingDat_Online.Human',    $sLvuGitRef, "cd /d $sLvuTestPath && git pull && ( fork_test.sh Human || echo ok )");
        getProjectStatus('RingDat_Online.IBBL',     $sLvuGitRef, "cd /d $sLvuTestPath && git pull && ( fork_test.sh IBBL || echo ok )");
        getProjectStatus('RingDat_Online.InstitutEignungspruefung', $sLvuGitRef, "cd /d $sLvuTestPath && git pull && ( fork_test.sh InstitutEignungspruefung || echo ok )");
        getProjectStatus('RingDat_Online.UBA-Wien', $sLvuGitRef, "cd /d $sLvuTestPath && git pull && ( fork_test.sh UBA-Wien || echo ok )");
        getProjectStatus('RingDat_Online.Eurofins', $sLvuGitRef, "cd /d $sLvuTestPath && git pull && ( fork_test.sh Eurofins || echo ok )");
        getProjectStatus('RingDat_Online.NIST-OWM', $sLvuGitRef, "cd /d $sLvuTestPath && git pull && ( fork_test.sh NIST-OWM || echo ok )");
        getProjectStatus('RingDat_Online.NIST-MML', $sLvuGitRef, "cd /d $sLvuTestPath && git pull && ( fork_test.sh NIST-MML || echo ok )");
        getProjectStatus('RingDat_Online.RKI',      $sLvuGitRef, "cd /d $sLvuTestPath && git pull && ( fork_test.sh RKI || echo ok )");
    }
    if (in_array($sHost, array( 'reinecke01-pc' ))) {
        getProjectStatus('LPP.AOCS',
            'C:\Users\oscar.reinecke\lpp\.git\refs\heads\master',
            'cd C:\Users\oscar.reinecke\lpp\admin\tests\PhantomJS && ( fork_test.sh || echo ok )');
        getProjectStatus('RingDat_Online.NIST-MML',
            'C:\xampp.htdocs\rdo\.git\refs\heads\master',
            'cd C:\xampp\htdocs\rdo\tests\PhantomJS && ( fork_test.sh NIST-MML || echo ok )');
    }
    if (in_array($sHost, array('noack-kopie01-pc', 'noack-pc'))) {
        getProjectStatus('LPP.AOCS',
            'C:\railo\tomcat\webapps\ROOT\.git\refs\heads\master',
            'cd C:\railo\tomcat\webapps\ROOT\admin\tests\PhantomJS && git pull && ( fork_test.sh || echo ok )');
    }

    checkFurtherImageConversions();
}

function checkFurtherImageConversions() {
    global $bNeedsFurtherConversions, $sScreenshotName, $smarty;
    if (empty($bNeedsFurtherConversions))
        return;
    if (empty($smarty))
        return;

    $smarty->assign("iframeFurtherImageConversions", $bNeedsFurtherConversions);
    $smarty->assign("sScreenshotName", $sScreenshotName);
}

function removeRunningTestFolder() {
    $sRunningProcessFolerPl = '"C:\\xampp\\htdocs\\DelphiScreenshotTestsuite\\html\\RunningProcess" /s /q';
    $sCmd = "rmdir " . $sRunningProcessFolerPl;
    exec($sCmd);
}

function killRunningProcess() {
    global $sAhkCmd, $sAhkFolderPl;
    $sCheckRunningTestsScript = $sAhkFolderPl . '/auf laufende Tests pruefen.ahk';
    $sCmd = "$sAhkCmd \"$sCheckRunningTestsScript\" killProcess";

    removeRunningTestFolder();
    $sLastLine = exec($sCmd, $aOutput, $iStatus);
    if ($iStatus) die("<h1>Fehler</h1>$sLastLine<br><tt>$sCmd</tt>");
}