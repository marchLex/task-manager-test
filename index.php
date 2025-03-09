<!doctype html>
<html lang="ru">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>TODO-list</title>
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<link type="image/x-icon" rel="shortcut icon" href="/favicon.ico">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">

		<meta name="robots" content="noindex, nofollow">
		<meta name="description" content="Простой TODO-list">
		
		<link href="assets/style.css?<?=filemtime(__DIR__ . "/assets/style.css")?>" type="text/css" rel="stylesheet">
	</head>
	
	<body>
		<main id="wrap">
			<header>
				<a href="/">
					<img src="/images/logo_icon.svg" alt="mrLexndr logo" class="logo" width="150" height="150">
				</a>
				
				<h1>
					Task Manager
				</h1>
			</header>
			
			<section id="search">
				<h2>
					Поиск
				</h2>
				
				<form method="post" id="tasksearch">
					<input type="text" name="search" value="" placeholder="Поиск по названию, содержанию и тегам" required>
					
					<button type="submit" class="button-basic">
						Найти
					</button>
					
					<button class="button-basic" onclick="updateTaskList();return false;">
						Очистить поиск
					</button>
				</form>
			</section>
			
			<section id="tasks">
				<h2>
					Задачи
				</h2>
				
				<form method="post" id="taskupdate">
					<input type="text" name="NAME" value="" placeholder="Название задачи" required>
					<input type="text" name="TAGS" value="" placeholder="Теги через запятую">
					<textarea name="DESCRIPTION" placeholder="Описание задачи"></textarea>
					<input type="hidden" name="action" value="update">
					<input type="hidden" name="id" value="0">
					<button type="submit" class="button-basic">
						Отправить
					</button>
				</form>
				
				<ul id="tasklist">
					<?if (!empty($currentUserTasks)) {?>
						<?foreach ($currentUserTasks as $currentUserTask) {?>
							<li>
								<?=$currentUserTask["NAME"]?>
							</li>
						<?}?>
					<?}?>
				</ul>
			</section>
			
			<div id="pagination"></div>
		</main>
		
		<script src="assets/script.js?<?=filemtime(__DIR__ . "/assets/script.js")?>"></script>
	</body>
</html>