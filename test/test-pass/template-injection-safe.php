<?php
// =============================================================================
// TEST-PASS: template injection (SSTI) — safe patterns must produce 0 findings
// Scan with: semgrep scan --config php/ test/test-pass/template-injection-safe.php
// =============================================================================

function safe_ssti_static_template() {
    $twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates'));
    return $twig->render('form.twig', array('value' => 'static'));
}

function safe_ssti_request_as_context() {
    if (!isset($_POST['value'])) {
        return;
    }
    $value = sanitize_text_field($_POST['value']);
    $twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates'));
    return $twig->render('form.twig', array('value' => $value));
}

function safe_ssti_static_loader() {
    $twig = new \Twig\Environment(new \Twig\Loader\ArrayLoader(array('tpl' => 'Hello')));
    $template = $twig->load('tpl');
    return $template->render(array('value' => 'static'));
}

function safe_ssti_timber_static() {
    return Timber::compile_string('Hello', array('greeting' => 'hello'));
}

function safe_ssti_sanitized_template_name() {
    if (!isset($_GET['layout'])) {
        return;
    }
    $layout = sanitize_text_field($_GET['layout']);
    $twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates'));
    return $twig->render($layout . '.twig', array('value' => 'static'));
}
