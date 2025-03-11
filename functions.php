<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule('iblock');

class TaskManager {
	
	private const IBLOCK_ID_TASKS = 2; // ID инфоблока с тасками
	
	function __construct() {
        $this->taskManagerInstance = \Bitrix\Iblock\Iblock::wakeUp(self::IBLOCK_ID_TASKS)->getEntityDataClass();
        $this->userId = session_id() ?: "temp"; //Делаем разные листы для каждого браузера и проверяем, есть ли ID сессии
        $this->logFile = __DIR__ . "/log.txt";
    }
	
	public function userTasksGetList(int $pageSize = 5, int $offset = 0, string $search = "") : array {
		/**
		* Дабл чек на размер листа и смещение
		*/
		if ($pageSize <= 0) {
			$pageSize = 5;
		}
		if ($offset < 0) {
			$offset = 0;
		}
		
		$tasksFilter = [
			'=TASK_MANAGER_USER_ID.VALUE' => $this->userId
			// "=TASK_MANAGER_USER_ID.VALUE" => "temp" //Временно для отладки
		];
		
		if ($search) {
			$tasksFilter[] = [
				"LOGIC" => "OR",
				"NAME" => "%" . $search . "%",
				// "DESCRIPTION" => "%" . $search . "%",
				// "TAGS" => "%" . $search . "%"
			];
		}
		
		$query = $this->taskManagerInstance::getList([
			"select" => [
				"ID", //ID задачи
				"NAME", //Название задачи
				"TAGS", //Теги
				"DATE" => "DATE_CREATE", //Дата создания задачи
				"DESCRIPTION" => "DETAIL_TEXT", //Описание задачи
			],
			"filter" => $tasksFilter,
			"limit" => $pageSize, //Размер страницы
			"offset" => $offset * $pageSize, //Отступ слева
			"count_total" => 1,
			"order" => [
				"ID" => "DESC"
			],
			"cache" => [
				"ttl" => 0
			],
		]);
		
		$tasks = [
			"items" => $query->fetchAll(),
			"count" => $query->getCount(),
			"pageSize" => $pageSize
		];
		
		
		// arshow($tasks);
		return $tasks;
	}
	
	/**
	* param array $dataToUpdate массив данных для добавления или апдейта таска
	*/
	
	public function userTasksUpdate(array $dataToUpdate) : int { //TODO: обновить сервер на PHP 8+ и переделать логику на : int|bool. Позволит оптимизировать код и улучшить читаемость
		/**
		* Используем старое API, т.к. методы update и add заблокированы в ORM для инфоблоков
		*/
		
		$dataToUpdate["id"] = intval($dataToUpdate["id"]); //Меняем тип на int, т.к. из JS приходит строка
		
		$updateTask = new CIBlockElement;
		
		$taskData = [
			"ACTIVE" => "Y",
			"NAME" => $dataToUpdate["NAME"],
			"IBLOCK_ID" => self::IBLOCK_ID_TASKS,
			"TAGS" => $dataToUpdate["TAGS"],
			"DETAIL_TEXT" => $dataToUpdate["DESCRIPTION"],
		];
		
		if ($dataToUpdate["id"] === 0) {
			$dataToUpdate["PROPERTIES"]["TASK_MANAGER_USER_ID"] = $this->userId;
			$taskData["PROPERTY_VALUES"] = $dataToUpdate["PROPERTIES"];
			
			if ($taskId = $updateTask->Add($taskData)) {
				return $taskId;
			} else {
				file_put_contents($this->logFile, date("d.m.Y H:i:s") . PHP_EOL . $updateTask->LAST_ERROR . PHP_EOL, FILE_APPEND);
			}
		} else {
			
			if ($updateTask->Update($dataToUpdate["id"], $taskData)) {
				CIBlockElement::SetPropertyValuesEx($dataToUpdate["id"], self::IBLOCK_ID_TASKS, $dataToUpdate["PROPERTIES"]);
				return $dataToUpdate["id"];
			} else {
				file_put_contents($this->logFile, date("d.m.Y H:i:s") . PHP_EOL . $updateTask->LAST_ERROR . PHP_EOL, FILE_APPEND);
			}
		}
		
		return 0;
	}
	
	public function userTasksDelete(int $taskId) : bool {
		if ($this->taskManagerInstance::delete($taskId)->isSuccess())
			return true;
		
		return false;
	}
}

$currentUserTaskManager = new TaskManager();

$postData = json_decode(file_get_contents('php://input'), true);
// echo $postData;
if ($postData["action"]) {
	switch ($postData["action"]) {
		case "delete":
			if ($postData["data"]["id"] && $postData["data"]["id"] >= 0)
				$currentUserTaskManager->userTasksDelete($postData["data"]["id"]);
			break;
			
		case "update":
			if (!empty($postData["data"]) && is_array($postData["data"]))
				$currentUserTaskManager->userTasksUpdate($postData["data"]);
			break;
			
		default:
			break;
	}
}

$page = intval($postData["data"]["page"] ?? 0);
$pageSize = 3;
$currentUserTasks = $currentUserTaskManager->userTasksGetList($pageSize, $page, $postData["data"]["search"] ?? "");

echo json_encode($currentUserTasks);