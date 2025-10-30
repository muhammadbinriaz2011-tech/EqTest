<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'db5xpbtbmv9ulr';
$username = 'ukrfhh29eellf';
$password = 'jua2ursxz7gb';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle routing
$page = $_GET['page'] ?? 'home';

// Sample questions and answers (in case database is not available)
$sample_questions = [
    [
        'id' => 1,
        'question' => 'When you make a mistake at work, how do you typically react?',
        'category' => 'self_awareness',
        'answers' => [
            ['id' => 1, 'text' => 'Get defensive and blame others', 'score' => 1],
            ['id' => 2, 'text' => 'Feel embarrassed but try to hide it', 'score' => 2],
            ['id' => 3, 'text' => 'Acknowledge the mistake and learn from it', 'score' => 3],
            ['id' => 4, 'text' => 'Take responsibility and proactively fix the issue', 'score' => 4]
        ]
    ],
    [
        'id' => 2,
        'question' => 'How do you usually handle criticism from others?',
        'category' => 'self_awareness',
        'answers' => [
            ['id' => 5, 'text' => 'Get angry and defensive', 'score' => 1],
            ['id' => 6, 'text' => 'Feel hurt but keep quiet', 'score' => 2],
            ['id' => 7, 'text' => 'Listen and consider if there is truth to it', 'score' => 3],
            ['id' => 8, 'text' => 'Thank them and ask for specific examples to improve', 'score' => 4]
        ]
    ],
    [
        'id' => 3,
        'question' => 'When someone is clearly upset, what is your first instinct?',
        'category' => 'empathy',
        'answers' => [
            ['id' => 9, 'text' => 'Avoid them or change the subject', 'score' => 1],
            ['id' => 10, 'text' => 'Feel uncomfortable and don\'t know what to say', 'score' => 2],
            ['id' => 11, 'text' => 'Ask them what\'s wrong and listen', 'score' => 3],
            ['id' => 12, 'text' => 'Show genuine concern and offer support', 'score' => 4]
        ]
    ],
    [
        'id' => 4,
        'question' => 'How do you react when you see someone being treated unfairly?',
        'category' => 'empathy',
        'answers' => [
            ['id' => 13, 'text' => 'Stay out of it to avoid drama', 'score' => 1],
            ['id' => 14, 'text' => 'Feel bad but don\'t intervene', 'score' => 2],
            ['id' => 15, 'text' => 'Speak up if it\'s appropriate', 'score' => 3],
            ['id' => 16, 'text' => 'Take action to help the person being treated unfairly', 'score' => 4]
        ]
    ],
    [
        'id' => 5,
        'question' => 'When you feel angry, what do you usually do?',
        'category' => 'emotional_regulation',
        'answers' => [
            ['id' => 17, 'text' => 'Lash out immediately', 'score' => 1],
            ['id' => 18, 'text' => 'Suppress it until you can\'t anymore', 'score' => 2],
            ['id' => 19, 'text' => 'Take a moment to calm down before responding', 'score' => 3],
            ['id' => 20, 'text' => 'Use it as motivation to address the underlying issue', 'score' => 4]
        ]
    ],
    [
        'id' => 6,
        'question' => 'How do you handle stressful situations?',
        'category' => 'emotional_regulation',
        'answers' => [
            ['id' => 21, 'text' => 'Panic and feel overwhelmed', 'score' => 1],
            ['id' => 22, 'text' => 'Avoid dealing with it', 'score' => 2],
            ['id' => 23, 'text' => 'Break it down into manageable steps', 'score' => 3],
            ['id' => 24, 'text' => 'Stay calm and tackle it systematically', 'score' => 4]
        ]
    ],
    [
        'id' => 7,
        'question' => 'In a group discussion, how do you typically participate?',
        'category' => 'social_skills',
        'answers' => [
            ['id' => 25, 'text' => 'Stay quiet and avoid speaking up', 'score' => 1],
            ['id' => 26, 'text' => 'Only speak when directly asked', 'score' => 2],
            ['id' => 27, 'text' => 'Contribute ideas when relevant', 'score' => 3],
            ['id' => 28, 'text' => 'Actively engage and encourage others to participate', 'score' => 4]
        ]
    ],
    [
        'id' => 8,
        'question' => 'How do you approach resolving conflicts with colleagues?',
        'category' => 'social_skills',
        'answers' => [
            ['id' => 29, 'text' => 'Avoid the person entirely', 'score' => 1],
            ['id' => 30, 'text' => 'Give them the silent treatment', 'score' => 2],
            ['id' => 31, 'text' => 'Try to have a calm conversation about it', 'score' => 3],
            ['id' => 32, 'text' => 'Seek to understand their perspective and find common ground', 'score' => 4]
        ]
    ]
];

// Handle quiz submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answers'])) {
    $answers = $_POST['answers'];
    $total_score = 0;
    $category_scores = ['self_awareness' => 0, 'empathy' => 0, 'emotional_regulation' => 0, 'social_skills' => 0];
    $category_counts = ['self_awareness' => 0, 'empathy' => 0, 'emotional_regulation' => 0, 'social_skills' => 0];
    
    foreach ($answers as $question_id => $answer_id) {
        $question = $sample_questions[$question_id - 1];
        $category = $question['category'];
        
        foreach ($question['answers'] as $answer) {
            if ($answer['id'] == $answer_id) {
                $total_score += $answer['score'];
                $category_scores[$category] += $answer['score'];
                $category_counts[$category]++;
                break;
            }
        }
    }
    
    $max_score = count($sample_questions) * 4;
    $percentage = round(($total_score / $max_score) * 100);
    
    // Calculate category percentages
    foreach ($category_scores as $category => $score) {
        if ($category_counts[$category] > 0) {
            $category_scores[$category] = round(($score / ($category_counts[$category] * 4)) * 100);
        }
    }
    
    // Determine EQ level
    if ($percentage >= 85) {
        $eq_level = 'Exceptional';
        $eq_description = 'You have exceptional emotional intelligence!';
        $eq_color = '#48bb78';
    } elseif ($percentage >= 70) {
        $eq_level = 'High';
        $eq_description = 'You have high emotional intelligence.';
        $eq_color = '#38b2ac';
    } elseif ($percentage >= 55) {
        $eq_level = 'Good';
        $eq_description = 'You have good emotional intelligence.';
        $eq_color = '#ed8936';
    } elseif ($percentage >= 40) {
        $eq_level = 'Developing';
        $eq_description = 'Your emotional intelligence is developing.';
        $eq_color = '#f56565';
    } else {
        $eq_level = 'Needs Improvement';
        $eq_description = 'There\'s room for growth in your emotional intelligence.';
        $eq_color = '#e53e3e';
    }
    
    $page = 'results';
}

function getCSS() {
    return '
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { text-align: center; margin-bottom: 40px; color: white; }
        header h1 { font-size: 3rem; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .subtitle { font-size: 1.2rem; opacity: 0.9; }
        main { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); margin-bottom: 40px; }
        .intro h2 { color: #4a5568; margin-bottom: 20px; font-size: 2rem; }
        .intro p { font-size: 1.1rem; margin-bottom: 30px; color: #666; }
        .benefits { background: #f7fafc; padding: 30px; border-radius: 15px; border-left: 5px solid #667eea; }
        .benefits h3 { color: #4a5568; margin-bottom: 20px; font-size: 1.3rem; }
        .benefits ul { list-style: none; }
        .benefits li { padding: 10px 0; font-size: 1rem; color: #555; }
        .categories { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .category { background: #f8f9fa; padding: 25px; border-radius: 15px; text-align: center; border: 2px solid transparent; transition: all 0.3s ease; }
        .category:hover { border-color: #667eea; transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .category h4 { color: #4a5568; margin-bottom: 10px; font-size: 1.2rem; }
        .category p { color: #666; font-size: 0.95rem; }
        .start-section { text-align: center; padding: 40px 0; }
        .start-btn { display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 18px 40px; text-decoration: none; border-radius: 50px; font-size: 1.2rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3); }
        .start-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4); }
        .progress-bar { background: #e2e8f0; height: 8px; border-radius: 4px; margin-bottom: 30px; overflow: hidden; }
        .progress-fill { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100%; transition: width 0.3s ease; }
        .question-counter { text-align: center; color: #666; margin-bottom: 30px; font-size: 1.1rem; }
        .question h3 { color: #4a5568; font-size: 1.5rem; margin-bottom: 30px; line-height: 1.4; }
        .answers { display: flex; flex-direction: column; gap: 15px; }
        .answer-option { background: #f8f9fa; border: 2px solid #e2e8f0; border-radius: 15px; padding: 20px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 15px; }
        .answer-option:hover { border-color: #667eea; background: #f0f4ff; }
        .answer-option.selected { border-color: #667eea; background: #e6f3ff; }
        .answer-option input[type="radio"] { margin: 0; width: 20px; height: 20px; accent-color: #667eea; }
        .answer-option label { cursor: pointer; flex: 1; font-size: 1rem; color: #4a5568; }
        .navigation { display: flex; justify-content: space-between; align-items: center; margin-top: 40px; padding-top: 30px; border-top: 1px solid #e2e8f0; }
        .btn { padding: 12px 30px; border: none; border-radius: 25px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; text-align: center; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3); }
        .btn-secondary { background: #e2e8f0; color: #4a5568; }
        .btn-secondary:hover { background: #cbd5e0; }
        .score-display { text-align: center; margin-bottom: 40px; }
        .score-circle { width: 200px; height: 200px; border-radius: 50%; background: conic-gradient(#667eea 0deg, #667eea var(--score-angle), #e2e8f0 var(--score-angle)); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; position: relative; }
        .score-circle::before { content: ""; position: absolute; width: 160px; height: 160px; background: white; border-radius: 50%; }
        .score-text { position: relative; z-index: 1; font-size: 2.5rem; font-weight: bold; color: #4a5568; }
        .score-label { font-size: 1.2rem; color: #666; margin-top: 10px; }
        .eq-level { font-size: 1.5rem; font-weight: bold; margin: 20px 0; padding: 15px; border-radius: 15px; background: #f0f4ff; color: #4a5568; }
        .category-scores { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 40px 0; }
        .category-score { background: #f8f9fa; padding: 25px; border-radius: 15px; text-align: center; }
        .category-score h4 { color: #4a5568; margin-bottom: 15px; font-size: 1.2rem; }
        .category-score .score { font-size: 2rem; font-weight: bold; color: #667eea; margin-bottom: 10px; }
        .category-score .max-score { color: #666; font-size: 0.9rem; }
        .feedback { background: #f7fafc; padding: 30px; border-radius: 15px; border-left: 5px solid #667eea; margin: 40px 0; }
        .feedback h3 { color: #4a5568; margin-bottom: 20px; font-size: 1.5rem; }
        .feedback p { color: #666; line-height: 1.6; margin-bottom: 15px; }
        .recommendations { background: #fff5f5; padding: 30px; border-radius: 15px; border-left: 5px solid #f56565; margin: 40px 0; }
        .recommendations h3 { color: #4a5568; margin-bottom: 20px; font-size: 1.5rem; }
        .recommendations ul { list-style: none; }
        .recommendations li { padding: 10px 0; color: #666; position: relative; padding-left: 25px; }
        .recommendations li::before { content: "💡"; position: absolute; left: 0; }
        .action-buttons { display: flex; gap: 20px; justify-content: center; margin-top: 40px; flex-wrap: wrap; }
        footer { text-align: center; color: white; opacity: 0.8; }
        @media (max-width: 768px) {
            .container { padding: 10px; }
            header h1 { font-size: 2rem; }
            main { padding: 20px; }
            .categories { grid-template-columns: 1fr; }
            .score-circle { width: 150px; height: 150px; }
            .score-circle::before { width: 120px; height: 120px; }
            .score-text { font-size: 2rem; }
            .category-scores { grid-template-columns: 1fr; }
            .action-buttons { flex-direction: column; align-items: center; }
            .navigation { flex-direction: column; gap: 15px; }
        }
    </style>';
}

function getJavaScript() {
    return '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const answerOptions = document.querySelectorAll(".answer-option");
            answerOptions.forEach(option => {
                option.addEventListener("click", function() {
                    answerOptions.forEach(opt => opt.classList.remove("selected"));
                    this.classList.add("selected");
                    const radio = this.querySelector("input[type=\"radio\"]");
                    radio.checked = true;
                });
                const radio = option.querySelector("input[type=\"radio\"]");
                radio.addEventListener("change", function() {
                    if (this.checked) {
                        answerOptions.forEach(opt => opt.classList.remove("selected"));
                        option.classList.add("selected");
                    }
                });
            });
        });
    </script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page === 'home' ? 'Emotional Intelligence Test' : ($page === 'quiz' ? 'EQ Test - Question ' . ($_GET['q'] ?? 1) : 'EQ Test Results'); ?></title>
    <?php echo getCSS(); ?>
</head>
<body>
    <div class="container">
        <header>
            <h1>🧠 Emotional Intelligence Assessment</h1>
            <p class="subtitle">
                <?php 
                if ($page === 'home') echo 'Discover your EQ and unlock your emotional potential';
                elseif ($page === 'quiz') echo 'Question ' . ($_GET['q'] ?? 1) . ' of ' . count($sample_questions);
                else echo 'Discover your emotional intelligence insights';
                ?>
            </p>
        </header>

        <main>
            <?php if ($page === 'home'): ?>
                <!-- Homepage -->
                <section class="intro">
                    <div class="intro-content">
                        <h2>What is Emotional Intelligence?</h2>
                        <p>Emotional Intelligence (EQ) is the ability to recognize, understand, and manage your own emotions, as well as the emotions of others. It's a crucial skill that affects every aspect of your personal and professional life.</p>
                        
                        <div class="benefits">
                            <h3>Why EQ Matters:</h3>
                            <ul>
                                <li>✨ Better relationships with family, friends, and colleagues</li>
                                <li>🎯 Improved decision-making and problem-solving</li>
                                <li>💪 Enhanced leadership and teamwork skills</li>
                                <li>😌 Better stress management and mental health</li>
                                <li>📈 Increased career success and satisfaction</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section class="test-info">
                    <h3>About This Test</h3>
                    <p>Our comprehensive EQ assessment includes <?php echo count($sample_questions); ?> carefully crafted questions that evaluate four key areas:</p>
                    
                    <div class="categories">
                        <div class="category">
                            <h4>🎭 Self-Awareness</h4>
                            <p>Understanding your own emotions and their impact</p>
                        </div>
                        <div class="category">
                            <h4>💝 Empathy</h4>
                            <p>Recognizing and understanding others' emotions</p>
                        </div>
                        <div class="category">
                            <h4>⚖️ Emotional Regulation</h4>
                            <p>Managing and controlling your emotional responses</p>
                        </div>
                        <div class="category">
                            <h4>🤝 Social Skills</h4>
                            <p>Building and maintaining healthy relationships</p>
                        </div>
                    </div>
                </section>

                <section class="start-section">
                    <div class="test-duration">
                        <p>⏱️ <strong>Time Required:</strong> 5-10 minutes</p>
                        <p>📊 <strong>Results:</strong> Detailed feedback with improvement suggestions</p>
                    </div>
                    
                    <a href="?page=quiz&q=1" class="start-btn">
                        <span>Start Your EQ Assessment</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </section>

            <?php elseif ($page === 'quiz'): ?>
                <!-- Quiz Page -->
                <?php 
                $current_question = (int)($_GET['q'] ?? 1);
                if ($current_question < 1 || $current_question > count($sample_questions)) {
                    header('Location: ?page=quiz&q=1');
                    exit;
                }
                $question = $sample_questions[$current_question - 1];
                $progress = ($current_question / count($sample_questions)) * 100;
                ?>
                
                <div class="quiz-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                    </div>

                    <div class="question-counter">
                        Question <?php echo $current_question; ?> of <?php echo count($sample_questions); ?>
                    </div>

                    <form method="POST" action="?page=results">
                        <div class="question">
                            <h3><?php echo htmlspecialchars($question['question']); ?></h3>
                            
                            <div class="answers">
                                <?php foreach ($question['answers'] as $answer): ?>
                                    <div class="answer-option">
                                        <input type="radio" 
                                               name="answers[<?php echo $question['id']; ?>]" 
                                               value="<?php echo $answer['id']; ?>" 
                                               id="answer_<?php echo $answer['id']; ?>"
                                               required>
                                        <label for="answer_<?php echo $answer['id']; ?>">
                                            <?php echo htmlspecialchars($answer['text']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="navigation">
                            <div>
                                <?php if ($current_question > 1): ?>
                                    <a href="?page=quiz&q=<?php echo $current_question - 1; ?>" class="btn btn-secondary">
                                        ← Previous
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <?php if ($current_question < count($sample_questions)): ?>
                                    <a href="?page=quiz&q=<?php echo $current_question + 1; ?>" class="btn btn-primary" onclick="this.closest('form').submit(); return false;">
                                        Next →
                                    </a>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-primary">
                                        Finish Test
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>

            <?php elseif ($page === 'results'): ?>
                <!-- Results Page -->
                <div class="results-container">
                    <div class="score-display">
                        <div class="score-circle" style="--score-angle: <?php echo ($percentage / 100) * 360; ?>deg;">
                            <div class="score-text"><?php echo $percentage; ?>%</div>
                        </div>
                        <div class="score-label">Total EQ Score</div>
                        <div class="eq-level" style="color: <?php echo $eq_color; ?>">
                            <?php echo $eq_level; ?> Emotional Intelligence
                        </div>
                        <p style="color: #666; font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
                            <?php echo $eq_description; ?>
                        </p>
                    </div>

                    <div class="category-scores">
                        <div class="category-score">
                            <h4>🎭 Self-Awareness</h4>
                            <div class="score"><?php echo $category_scores['self_awareness']; ?>%</div>
                            <div class="max-score">Understanding your emotions</div>
                        </div>
                        <div class="category-score">
                            <h4>💝 Empathy</h4>
                            <div class="score"><?php echo $category_scores['empathy']; ?>%</div>
                            <div class="max-score">Understanding others' emotions</div>
                        </div>
                        <div class="category-score">
                            <h4>⚖️ Emotional Regulation</h4>
                            <div class="score"><?php echo $category_scores['emotional_regulation']; ?>%</div>
                            <div class="max-score">Managing your emotions</div>
                        </div>
                        <div class="category-score">
                            <h4>🤝 Social Skills</h4>
                            <div class="score"><?php echo $category_scores['social_skills']; ?>%</div>
                            <div class="max-score">Building relationships</div>
                        </div>
                    </div>

                    <div class="feedback">
                        <h3>Your Emotional Intelligence Profile</h3>
                        <p>Based on your responses, you demonstrate <?php echo strtolower($eq_level); ?> emotional intelligence. This assessment provides insights into how you perceive and manage emotions in various situations.</p>
                        <p>Your results show strengths in certain areas and opportunities for growth in others. Emotional intelligence is a skill that can be developed and improved over time with practice and awareness.</p>
                    </div>

                    <div class="recommendations">
                        <h3>Recommendations for Growth</h3>
                        <ul>
                            <?php if ($category_scores['self_awareness'] < 70): ?>
                                <li>Practice mindfulness and self-reflection daily to improve self-awareness</li>
                                <li>Keep an emotion journal to track your feelings and triggers</li>
                            <?php endif; ?>
                            <?php if ($category_scores['empathy'] < 70): ?>
                                <li>Practice active listening and try to see situations from others' perspectives</li>
                                <li>Ask open-ended questions to better understand others' feelings</li>
                            <?php endif; ?>
                            <?php if ($category_scores['emotional_regulation'] < 70): ?>
                                <li>Learn deep breathing and relaxation techniques for stress management</li>
                                <li>Practice pausing before reacting to emotional triggers</li>
                            <?php endif; ?>
                            <?php if ($category_scores['social_skills'] < 70): ?>
                                <li>Practice assertive communication and conflict resolution skills</li>
                                <li>Work on building and maintaining positive relationships</li>
                            <?php endif; ?>
                            <li>Continue practicing your emotional intelligence skills regularly</li>
                            <li>Consider reading books or taking courses on emotional intelligence</li>
                        </ul>
                    </div>

                    <div class="action-buttons">
                        <a href="?page=home" class="btn btn-primary">Take Test Again</a>
                        <button onclick="window.print()" class="btn btn-secondary">Print Results</button>
                        <button onclick="shareResults()" class="btn btn-secondary">Share Results</button>
                    </div>
                </div>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; 2024 EQ Assessment Platform. All rights reserved.</p>
        </footer>
    </div>

    <?php 
    if ($page === 'quiz') echo getJavaScript();
    if ($page === 'results'): 
    ?>
    <script>
        function shareResults() {
            const score = <?php echo $percentage; ?>;
            const level = "<?php echo $eq_level; ?>";
            const text = `I just took an Emotional Intelligence test and scored ${score}% - ${level} EQ! Take the test yourself to discover your emotional intelligence.`;
            
            if (navigator.share) {
                navigator.share({
                    title: 'My EQ Test Results',
                    text: text,
                    url: window.location.origin + window.location.pathname + '?page=home'
                });
            } else {
                navigator.clipboard.writeText(text + ' ' + window.location.origin + window.location.pathname + '?page=home').then(() => {
                    alert('Results copied to clipboard!');
                });
            }
        }
    </script>
    <?php endif; ?>
</body>
</html>
