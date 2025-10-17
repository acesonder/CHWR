<?php
/**
 * Assessment Section - Individual Section View
 */

session_start();

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../../index.php');
    exit;
}

$sectionCode = $_GET['section'] ?? '';
if (empty($sectionCode) || !preg_match('/^[A-L]$/', $sectionCode)) {
    header('Location: assessment.php');
    exit;
}

$config = require __DIR__ . '/../../config/config.php';
$firstName = $_SESSION['first_name'] ?? 'User';
$userId = $_SESSION['user_id'] ?? 0;

$db = Database::getInstance()->getConnection();

// Get assessment
$stmt = $db->prepare("
    SELECT * FROM assessments 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 1
");
$stmt->execute([$userId]);
$assessment = $stmt->fetch();

if (!$assessment) {
    header('Location: assessment.php');
    exit;
}

$assessmentId = $assessment['assessment_id'];

// Get section info
$stmt = $db->prepare("
    SELECT * FROM assessment_sections 
    WHERE assessment_id = ? AND section_code = ?
");
$stmt->execute([$assessmentId, $sectionCode]);
$section = $stmt->fetch();

if (!$section) {
    header('Location: assessment.php');
    exit;
}

// Get existing responses for this section
$stmt = $db->prepare("
    SELECT question_id, response_value 
    FROM assessment_responses 
    WHERE assessment_id = ? AND section_code = ?
");
$stmt->execute([$assessmentId, $sectionCode]);
$existingResponses = [];
while ($row = $stmt->fetch()) {
    $existingResponses[$row['question_id']] = $row['response_value'];
}

// Define questions for each section
$questions = [];

switch ($sectionCode) {
    case 'A': // Consent & Preferences
        $questions = [
            ['id' => 'consent_share', 'text' => 'I consent to sharing my information with service providers', 'type' => 'multiple_choice', 'options' => ['Yes', 'No', 'Undecided']],
            ['id' => 'consent_contact', 'text' => 'How would you prefer to be contacted?', 'type' => 'multi_select', 'options' => ['Phone Call', 'Text Message', 'Email', 'In Person']],
            ['id' => 'language_preference', 'text' => 'Preferred language for communication', 'type' => 'multiple_choice', 'options' => ['English', 'French', 'Other']],
            ['id' => 'accessibility_needs', 'text' => 'Do you have any accessibility needs?', 'type' => 'text'],
            ['id' => 'communication_notes', 'text' => 'Any other communication preferences or notes?', 'type' => 'text']
        ];
        break;
        
    case 'B': // Mental Health, Safety, Crisis
        $questions = [
            ['id' => 'mental_health_concern', 'text' => 'Are you currently experiencing mental health concerns?', 'type' => 'multiple_choice', 'options' => ['No', 'Mild', 'Moderate', 'Severe'], 'critical' => true],
            ['id' => 'safety_concern', 'text' => 'Do you currently feel safe where you are staying?', 'type' => 'multiple_choice', 'options' => ['Yes', 'Somewhat', 'No'], 'critical' => true],
            ['id' => 'violence_risk', 'text' => 'Are you at risk of harm from another person?', 'type' => 'multiple_choice', 'options' => ['No', 'Unsure', 'Yes'], 'critical' => true],
            ['id' => 'self_harm', 'text' => 'Have you had thoughts of harming yourself in the past week?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes'], 'critical' => true],
            ['id' => 'crisis_support', 'text' => 'Would you like to speak with a crisis counselor?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes', 'Maybe later']],
            ['id' => 'mental_health_notes', 'text' => 'Additional notes about mental health or safety concerns', 'type' => 'text']
        ];
        break;
        
    case 'C': // Housing & Shelter
        $questions = [
            ['id' => 'current_housing', 'text' => 'What is your current housing situation?', 'type' => 'multiple_choice', 'options' => ['Shelter', 'Transitional Housing', 'Outdoors/Vehicle', 'Couch Surfing', 'Temporary with Family/Friends', 'Other']],
            ['id' => 'housing_stability', 'text' => 'How long have you been in your current situation?', 'type' => 'multiple_choice', 'options' => ['Less than 1 week', '1-4 weeks', '1-3 months', '3-6 months', '6+ months']],
            ['id' => 'housing_priority', 'text' => 'Is finding permanent housing your top priority?', 'type' => 'multiple_choice', 'options' => ['Yes', 'No', 'One of several priorities']],
            ['id' => 'housing_barriers', 'text' => 'What barriers prevent you from finding housing?', 'type' => 'multi_select', 'options' => ['Lack of income', 'Credit issues', 'Criminal record', 'No references', 'Pets', 'Discrimination', 'Other']],
            ['id' => 'housing_notes', 'text' => 'Additional housing information', 'type' => 'text']
        ];
        break;
        
    case 'D': // Medical, Dental, Vision
        $questions = [
            ['id' => 'health_status', 'text' => 'How would you rate your overall health?', 'type' => 'multiple_choice', 'options' => ['Excellent', 'Good', 'Fair', 'Poor']],
            ['id' => 'chronic_conditions', 'text' => 'Do you have any chronic medical conditions?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes - managed', 'Yes - not managed']],
            ['id' => 'medications', 'text' => 'Are you currently taking any medications?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes - as prescribed', 'Yes - but missing doses']],
            ['id' => 'dental_care', 'text' => 'Do you need dental care?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes - minor', 'Yes - urgent']],
            ['id' => 'vision_care', 'text' => 'Do you need vision care or glasses?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes']],
            ['id' => 'medical_notes', 'text' => 'Additional medical information', 'type' => 'text']
        ];
        break;
        
    case 'E': // Counseling / Support
        $questions = [
            ['id' => 'counseling_interest', 'text' => 'Are you interested in counseling or therapy?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes', 'Maybe', 'Already receiving']],
            ['id' => 'support_type', 'text' => 'What type of support would be most helpful?', 'type' => 'multi_select', 'options' => ['Individual counseling', 'Group therapy', 'Peer support', 'Family counseling', 'Other']],
            ['id' => 'trauma_history', 'text' => 'Would you like trauma-informed support services?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes', 'Unsure']],
            ['id' => 'support_notes', 'text' => 'Additional support needs', 'type' => 'text']
        ];
        break;
        
    case 'F': // Substance Use
        $questions = [
            ['id' => 'substance_use', 'text' => 'Do you currently use substances?', 'type' => 'multiple_choice', 'options' => ['No', 'Occasionally', 'Regularly']],
            ['id' => 'substance_concern', 'text' => 'Is substance use a concern for you?', 'type' => 'multiple_choice', 'options' => ['No', 'Somewhat', 'Yes']],
            ['id' => 'treatment_interest', 'text' => 'Are you interested in substance use treatment?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes', 'Maybe', 'Already in treatment']],
            ['id' => 'harm_reduction', 'text' => 'Would you like information about harm reduction services?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes']],
            ['id' => 'substance_notes', 'text' => 'Additional information', 'type' => 'text']
        ];
        break;
        
    case 'G': // Legal Issues
        $questions = [
            ['id' => 'legal_issues', 'text' => 'Do you currently have any legal issues?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes']],
            ['id' => 'legal_type', 'text' => 'What type of legal issues?', 'type' => 'multi_select', 'options' => ['Criminal charges', 'Court dates', 'Warrants', 'Family law', 'Housing disputes', 'Other']],
            ['id' => 'legal_aid', 'text' => 'Would you like a referral to legal aid?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes', 'Already have lawyer']],
            ['id' => 'legal_notes', 'text' => 'Additional legal information', 'type' => 'text']
        ];
        break;
        
    case 'H': // Income, Benefits, ID
        $questions = [
            ['id' => 'income_source', 'text' => 'What is your current source of income?', 'type' => 'multi_select', 'options' => ['Employment', 'ODSP', 'OW', 'EI', 'Pension', 'No income', 'Other']],
            ['id' => 'benefits_receiving', 'text' => 'Are you receiving all benefits you\'re entitled to?', 'type' => 'multiple_choice', 'options' => ['Yes', 'No', 'Unsure']],
            ['id' => 'id_status', 'text' => 'Do you have government-issued ID?', 'type' => 'multiple_choice', 'options' => ['Yes - valid', 'Yes - expired', 'No - lost/stolen', 'No - never had']],
            ['id' => 'sin_status', 'text' => 'Do you have a Social Insurance Number (SIN)?', 'type' => 'multiple_choice', 'options' => ['Yes', 'No', 'Unsure']],
            ['id' => 'income_notes', 'text' => 'Additional information', 'type' => 'text']
        ];
        break;
        
    case 'I': // Family & Social Network
        $questions = [
            ['id' => 'social_support', 'text' => 'Do you have supportive family or friends?', 'type' => 'multiple_choice', 'options' => ['Yes - strong support', 'Yes - limited support', 'No']],
            ['id' => 'children', 'text' => 'Do you have children?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes - with me', 'Yes - in care of others']],
            ['id' => 'isolation', 'text' => 'Do you feel socially isolated?', 'type' => 'multiple_choice', 'options' => ['No', 'Sometimes', 'Often']],
            ['id' => 'social_notes', 'text' => 'Additional information about support network', 'type' => 'text']
        ];
        break;
        
    case 'J': // Employment & Education
        $questions = [
            ['id' => 'employment_status', 'text' => 'Current employment status', 'type' => 'multiple_choice', 'options' => ['Employed full-time', 'Employed part-time', 'Unemployed - seeking', 'Unemployed - not seeking', 'Unable to work', 'Student']],
            ['id' => 'education_level', 'text' => 'Highest level of education completed', 'type' => 'multiple_choice', 'options' => ['Less than high school', 'High school', 'College/Trade', 'University', 'Post-graduate']],
            ['id' => 'employment_interest', 'text' => 'Are you interested in employment support?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes', 'Maybe']],
            ['id' => 'training_interest', 'text' => 'Are you interested in education or training programs?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes', 'Maybe']],
            ['id' => 'employment_notes', 'text' => 'Additional information', 'type' => 'text']
        ];
        break;
        
    case 'K': // Life Skills & Daily Living
        $questions = [
            ['id' => 'daily_living', 'text' => 'Do you need help with daily living tasks?', 'type' => 'multi_select', 'options' => ['Cooking', 'Cleaning', 'Budgeting', 'Transportation', 'Shopping', 'None', 'Other']],
            ['id' => 'life_skills', 'text' => 'Would you benefit from life skills training?', 'type' => 'multiple_choice', 'options' => ['No', 'Yes', 'Maybe']],
            ['id' => 'life_skills_notes', 'text' => 'Additional information', 'type' => 'text']
        ];
        break;
        
    case 'L': // Recovery Planning
        $questions = [
            ['id' => 'primary_goal', 'text' => 'What is your primary goal right now?', 'type' => 'text'],
            ['id' => 'support_needed', 'text' => 'What support do you need most to achieve your goals?', 'type' => 'text'],
            ['id' => 'strengths', 'text' => 'What are your personal strengths?', 'type' => 'text'],
            ['id' => 'recovery_notes', 'text' => 'Additional recovery planning information', 'type' => 'text']
        ];
        break;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section <?php echo $sectionCode; ?> - CHWR</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Global Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-left">
                <a href="../../dashboard.php" class="logo">
                    <i class="fas fa-heart"></i>
                    <span>CHWR</span>
                </a>
            </div>
            <div class="nav-right">
                <div class="profile-dropdown">
                    <div class="profile-avatar" onclick="CHWR.toggleProfileDropdown()">
                        <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                    </div>
                    <div class="dropdown-menu" id="profileDropdown">
                        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                        <button onclick="CHWR.toggleTheme()">
                            <i class="fas fa-moon"></i> Toggle Theme
                        </button>
                        <a href="../../api/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem; margin-bottom: 2rem;">
        <!-- Header -->
        <div style="margin-bottom: 2rem;">
            <a href="assessment.php" class="btn btn-outline" style="margin-bottom: 1rem;">
                <i class="fas fa-arrow-left"></i> Back to Assessment
            </a>
            <h1>Section <?php echo $sectionCode; ?>: <?php echo htmlspecialchars($section['section_name']); ?></h1>
            <p style="color: var(--text-secondary);">All questions are optional. Your answers are automatically saved.</p>
        </div>

        <!-- Section Form -->
        <div class="feature-card">
            <form id="sectionForm">
                <input type="hidden" name="assessment_id" value="<?php echo $assessmentId; ?>">
                <input type="hidden" name="section_code" value="<?php echo $sectionCode; ?>">
                
                <?php foreach ($questions as $index => $question): ?>
                    <div class="form-group" style="padding-bottom: 1.5rem; <?php echo $index < count($questions) - 1 ? 'border-bottom: 1px solid var(--border-color);' : ''; ?> margin-bottom: 1.5rem;">
                        <label>
                            <?php echo htmlspecialchars($question['text']); ?>
                            <?php if (isset($question['critical']) && $question['critical']): ?>
                                <span style="color: var(--danger-color); margin-left: 0.5rem;" title="Critical question">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </span>
                            <?php endif; ?>
                        </label>
                        
                        <?php
                        $currentValue = $existingResponses[$question['id']] ?? '';
                        
                        if ($question['type'] === 'multiple_choice'): ?>
                            <select name="<?php echo $question['id']; ?>" class="auto-save">
                                <option value="">-- Select an option --</option>
                                <?php foreach ($question['options'] as $option): ?>
                                    <option value="<?php echo htmlspecialchars($option); ?>"
                                        <?php echo $currentValue === $option ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($option); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                        <?php elseif ($question['type'] === 'multi_select'): ?>
                            <?php
                            $selectedValues = $currentValue ? json_decode($currentValue, true) : [];
                            if (!is_array($selectedValues)) $selectedValues = [];
                            ?>
                            <div style="margin-top: 0.5rem;">
                                <?php foreach ($question['options'] as $option): ?>
                                    <label style="display: block; margin-bottom: 0.5rem;">
                                        <input type="checkbox" 
                                               name="<?php echo $question['id']; ?>[]" 
                                               value="<?php echo htmlspecialchars($option); ?>"
                                               class="auto-save"
                                               <?php echo in_array($option, $selectedValues) ? 'checked' : ''; ?>>
                                        <?php echo htmlspecialchars($option); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            
                        <?php else: // text ?>
                            <textarea name="<?php echo $question['id']; ?>" 
                                      rows="3" 
                                      class="auto-save"
                                      placeholder="Type your answer here..."><?php echo htmlspecialchars($currentValue); ?></textarea>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button type="button" class="btn btn-primary" onclick="markComplete()">
                        <i class="fas fa-check"></i> Mark Section Complete
                    </button>
                    <a href="assessment.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to Overview
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        // Auto-save functionality
        document.querySelectorAll('.auto-save').forEach(element => {
            element.addEventListener('change', () => saveSection());
            if (element.tagName === 'TEXTAREA' || element.tagName === 'INPUT') {
                element.addEventListener('input', debounce(() => saveSection(), 2000));
            }
        });
        
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        async function saveSection() {
            const formData = new FormData(document.getElementById('sectionForm'));
            
            try {
                const response = await fetch('../../api/assessment/save_section.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSaveIndicator('Saved');
                }
            } catch (error) {
                console.error('Auto-save error:', error);
            }
        }
        
        async function markComplete() {
            // Save first
            await saveSection();
            
            // Mark as complete
            try {
                const response = await fetch('../../api/assessment/complete_section.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        assessment_id: <?php echo $assessmentId; ?>,
                        section_code: '<?php echo $sectionCode; ?>'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Section marked as complete!');
                    window.location.href = 'assessment.php';
                } else {
                    alert(data.message || 'Failed to mark section complete');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            }
        }
        
        function showSaveIndicator(text) {
            let indicator = document.getElementById('saveIndicator');
            
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'saveIndicator';
                indicator.style.cssText = `
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    background: var(--success-color);
                    color: white;
                    padding: 10px 20px;
                    border-radius: var(--radius-md);
                    box-shadow: var(--shadow-lg);
                    z-index: 9999;
                    opacity: 0;
                    transition: opacity 0.3s;
                `;
                document.body.appendChild(indicator);
            }
            
            indicator.textContent = text;
            indicator.style.opacity = '1';
            
            setTimeout(() => {
                indicator.style.opacity = '0';
            }, 2000);
        }
    </script>
</body>
</html>
