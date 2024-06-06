const inputs = [...document.querySelectorAll('.table input[type="file"]')];

console.log(inputs)
inputs.forEach((input) => {
  input.addEventListener("change", () => {
    console.log(input.files[0])
    input.closest('.table__file').querySelector('.table__file-button').innerText = 'Выбрано: ' + input.files[0].name
  });
});

document.querySelector('.table__button').addEventListener('click', () => {
  const nodes = document.querySelectorAll('.table__row tr');
  const last = nodes[nodes.length - 1];
  const clone = last.cloneNode(true); // Клонируем последний элемент
  document.querySelector('.table__row').appendChild(clone); // Добавляем клонированный элемент в конец таблицы
});

