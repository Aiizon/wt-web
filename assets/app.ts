import './styles/app.scss';
import * as bootstrap from 'bootstrap'

document.addEventListener('DOMContentLoaded', (): void => {
    (document.querySelectorAll('.collapse') as NodeList).forEach((collapse: Element): void => {
        new bootstrap.Collapse(collapse, {
            toggle: false
        })
    })
});