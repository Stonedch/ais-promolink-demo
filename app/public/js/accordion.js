const accordions = [...document.querySelectorAll('.accordion')]
accordions.forEach(el => {
  console.log(el)
  el.addEventListener('click', () => {
    el.classList.toggle('accordion-active')
  })
})