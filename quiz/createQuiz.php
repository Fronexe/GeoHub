<?php $title = "Sign Up"; require('../partials/boilerplate.inc.php')?>
<?php require('../controllers/quiz/createQuiz.inc.php')?>

<div class="container d-flex justify-content-center align-items-center my-5">
    <div class="row">
        <div class="col-xl-12">
            <div class="bg-color border card border-color-form shadow">
                <div class="card-body">
                    <h2 class="card-title text-black">Create Quiz</h2>
                    <form class="validated-form" method="POST" novalidate>
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="title">Title</label>
                                <input class="form-control" placeholder="Title" type="title" name="title" id="title" value="<?php if (isset($_POST["submit"] )) echo $title ?>" required autofocus>
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="timer">Timer</label>
                                <select name="timer" class="form-select" aria-label="Default select example">
                                    <option selected>Choose time</option>
                                    <option value="300">5 Minutes</option>
                                    <option value="600">10 Minutes</option>
                                    <option value="1200">20 Minutes</option>
                                    <option value="1800">30 Minutes</option>
                                    <option value="2400">40 Minutes</option>
                                    <option value="3000">50 Minutes</option>
                                    <option value="3600">60 Minutes</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-6">
                                <label class="form-label" for="noOfQuestions">Number of Questions</label>
                                <input class="form-control" placeholder="No. of Questions" type="number" name="noOfQuestions" id="noOfQuestions" value="<?php if (isset($_POST["submit"] )) echo $noOfQuestions ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-sm-12">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" placeholder="Description" rows="5" name="description" id="description" required><?php if (isset($_POST["submit"] )) echo $description ?></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" name="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('../partials/footer.inc.php')?>