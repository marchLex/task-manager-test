function updateTaskList({action = false, data = false, page = 0} = {}) {
    
	data = data ? data : {}; //дабл чек если data = false
	data.page = page;
	
	fetch("functions.php", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify({
				action: action,
				data: data
			}),
		})
		.then((response) => {
			if (!response.ok) {
				throw new Error("Похоже на ошибку сети");
			}
			return response.json();
		})
		.then((taskList) => {
			console.log(taskList);
			
			const taskListElement = document.querySelector("#tasklist");
			taskListElement.innerHTML = '';
			
			taskList.items.forEach(task => {
				const taskListItem = document.createElement("li");
				taskListItem.innerHTML = 
					`
						<span class='name'>${task.NAME}</span>
						<span class='description'>${task.DESCRIPTION}</span>
						<span class='tags'>${task.TAGS ? task.TAGS.split(',').map(tag => `<span class="tag button-basic" onclick="updateTaskList({data: {search: '${tag.trim()}'}})">${tag.trim()}</span>`).join(' ') : ''}</span>
						<span class='task-buttons'>
							<button class='button-basic' onclick='updateTaskList({data: {"id": ${task.ID}}, action: "delete"})'>
								Удалить
							</button>
							<button class='button-basic' onclick='editTask(${JSON.stringify(task)})'>
								Изменить
							</button>
						</span>
					`;
				taskListElement.append(taskListItem);
			});
			
			createPagination(taskList.count, taskList.pageSize, page);
		})
		.catch((error) => {
			console.error("Ошибочка: ", error);
		}
	);
}

function createPagination(totalCount, pageSize, currentPage) {
	const totalPages = Math.ceil(totalCount / pageSize);
	const paginationContainer = document.querySelector("#pagination");

	paginationContainer.innerHTML = ''; //Каждый раз обновляем контейнер

	if (totalPages <= 1) {
		return;
	}
	
	console.log(totalPages);

	const createPageLink = (pageNumber, text) => {
		const button = document.createElement("button");
		
		button.classList.add("button-basic");
		
		button.textContent = text || pageNumber;
		button.addEventListener("click", (event) => {
			updateTaskList({page: pageNumber});
		});
		paginationContainer.appendChild(button);
	};

	for (let i = 0; i < totalPages; i++) {
		createPageLink(i, i + 1);
	}
}

updateTaskList();

let taskForm = document.querySelector("#taskupdate");

function editTask(task) {
	taskForm.querySelector("[name='NAME']").value = task.NAME;
	taskForm.querySelector("[name='TAGS']").value = task.TAGS;
	taskForm.querySelector("[name='DESCRIPTION']").value = task.DESCRIPTION;
	taskForm.querySelector("[name='id']").value = task.ID;
	taskForm.querySelector("[name='action']").value = "update";
	taskForm.scrollIntoView({behavior: 'smooth', block: 'start'});
}

taskForm.addEventListener("submit", function(event) {
	event.preventDefault();

	if (taskForm.checkValidity()) {
		
		const formData = new FormData(taskForm);
		const data = {};
		for (const [key, value] of formData.entries()) {
			data[key] = value;
		}

		updateTaskList({
			action: "update",
			data: data
		});
		
		taskForm.reset();
		
	} else {
		console.log("Ошибочка в форме, проверить валидность");
	}
});

const searchForm = document.querySelector("#tasksearch");

searchForm.addEventListener("submit", function(event) {
	event.preventDefault();

	if (searchForm.checkValidity()) {
		
		const formData = new FormData(searchForm);
		const data = {};
		for (const [key, value] of formData.entries()) {
			data[key] = value;
		}

		updateTaskList({
			data: data
		});
		
	} else {
		console.log("Ошибочка в форме, проверить валидность");
	}
});