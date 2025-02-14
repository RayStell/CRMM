MicroModal.init();

document.querySelectorAll('.open').forEach((modal)=>{
    MicroModal.show(modal.getAttribute('id'));
});