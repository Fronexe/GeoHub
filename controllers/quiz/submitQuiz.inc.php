<?php 
session_start();
require_once('../../vendor/autoload.php');
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();
require('../../functions/connection.inc.php');
require('../../functions/functions.inc.php');
require("../../partials/regex.inc.php");

if(isset($_SESSION['userId']) && !empty($_SESSION['userId'])){
    if(isset($_POST['submit']) && isset($_GET['quizId'])){
        $timeLeft = $_POST['timeLeft'];
        $quizId = $_GET['quizId'];


        $query = "SELECT quiz.*, users.username FROM quiz INNER JOIN users ON quiz.uid = users.uid WHERE quiz.quizid = ?";
        $stmt = $db->prepare($query);
        $stmt->bindValue(1, $_GET['quizId'], PDO::PARAM_INT);
        $stmt->execute();
        $quizRow = $stmt->fetch();

        $correctAnswers = 0;
        $score = 0;
        $quizScore = 0;
        $wrongAnswers = 0;
        $markScheme = array();
        $mcqIndex = 0;
        
        $selectQuery = "SELECT * FROM questions WHERE questionId = ?";
        $stmt1 = $db->prepare($selectQuery);
        $query = "SELECT choice FROM choices WHERE questionId = ?";
        $stmt2 = $db->prepare($query);
        for($i = 0; $i < $quizRow['nQuestions']; $i++){
            $answerObject = json_decode($_POST['answers'][$i], true);
           
            $stmt1->bindValue(1, $answerObject['questionId'], PDO::PARAM_INT);
            $stmt1->execute();
            $answerRow = $stmt1->fetch();

            $markScheme[$i]['question'] = $answerRow['question'];
            $markScheme[$i]['type'] = $answerRow['type'];
            $markScheme[$i]['score'] = $answerRow['score'];
            $markScheme[$i]['answer'] = strtolower($answerRow['answer']);
            $markScheme[$i]['userAnswer'] = $answerObject['answer'];
            if($answerRow['type'] == "MCQ"){
                $stmt2->bindValue(1, $answerRow['questionId'], PDO::PARAM_INT);
                $stmt2->execute();
                $choicesRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                $markScheme[$i]['choices'][$mcqIndex] = $choicesRow [0]['choice'];
                $markScheme[$i]['choices'][$mcqIndex+1] = $choicesRow [1]['choice'];
                $markScheme[$i]['choices'][$mcqIndex+2] = $choicesRow [2]['choice'];
                $markScheme[$i]['choices'][$mcqIndex+3] = $choicesRow [3]['choice'];
                $mcqIndex += 4;
            } 

            $answerResult = strcasecmp($answerObject['answer'], $answerRow['answer']); 

            if($answerResult == 0){
                $correctAnswers++;
                $score += $answerRow['score'];
            }else{
                $wrongAnswers++;
            }
            $quizScore += $answerRow['score'];
        }
        $dateConducted = date("F d\, Y");
        $timeElapsed = (int)$quizRow['totalTime'] - (int)$timeLeft;
        $uid = $_SESSION['userId'];
        $quizId = $_GET['quizId'];
        
        $markScheme['quizId'] = $_GET['quizId'];
        $markScheme['correctAnswers'] = $correctAnswers;
        $markScheme['wrongAnswers'] = $wrongAnswers;
        $markScheme['timeElapsed'] = $timeElapsed;
        $markScheme['score'] = $score;
        $markScheme['quizScore'] = $quizScore;
        $markScheme['nQuestions'] = $quizRow['nQuestions'];

        $_SESSION['markScheme'] = $markScheme;

        $db->beginTransaction();
        $sql = $db->prepare("INSERT INTO results (score, timeElapsed, userId, quizId, dateConducted) VALUES (?, ?, ?, ?, ?)");
        $sql->execute([$score, $timeElapsed, $uid, $quizId, $dateConducted] );
        $db->commit();
        header("Location: /ITCS333-Project/quiz/resultsCheck.php");
    }


}else{
    setcookie("redirect", $url,0,'/');
    header("Location: /ITCS333-Project/auth/signin.php");
}

?>