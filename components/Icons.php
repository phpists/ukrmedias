<?php

namespace app\components;

class Icons {

    static public $printer = '<svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg"><path d="M13.5 6.1875H4.5V1.6875C4.5 0.755519 5.25552 0 6.1875 0H11.8125C12.7445 0 13.5 0.755519 13.5 1.6875V6.1875ZM18 5.625V12.9375C18 13.8695 17.2445 14.625 16.3125 14.625H15.1875V16.3125C15.1875 17.2445 14.432 18 13.5 18H4.5C3.56802 18 2.8125 17.2445 2.8125 16.3125V14.625H1.6875C0.755519 14.625 0 13.8695 0 12.9375V5.625C0 4.69302 0.755519 3.9375 1.6875 3.9375H2.8125V7.875H15.1875V3.9375H16.3125C17.2445 3.9375 18 4.69302 18 5.625ZM12.9375 11.8125H5.0625V15.75H12.9375V11.8125Z"></path></svg>';
    static public $list = '<svg><use xlink:href="img/sprite.svg#icon-tabs-list"></use></svg>';
    static public $xls = '<svg viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M2 .875C2 .668 2.168.5 2.375.5h7.28c.1 0 .195.04.265.11l3.97 3.97c.07.07.11.165.11.265v10.28a.375.375 0 0 1-.375.375H2.375A.375.375 0 0 1 2 15.125v-2.25c0-.207.168-.375.375-.375h.75c.207 0 .375.168.375.375v.75c0 .207.168.375.375.375h8.25a.375.375 0 0 0 .375-.375v-7.5a.375.375 0 0 0-.375-.375h-3a.375.375 0 0 1-.375-.375v-3A.375.375 0 0 0 8.375 2h-4.5a.375.375 0 0 0-.375.375v4.5a.375.375 0 0 1-.375.375h-.75A.375.375 0 0 1 2 6.875v-6z"/><path d="M1.038 10.746c.155 0 .283.047.385.14.103.09.154.207.154.35 0 .142-.051.26-.154.352a.559.559 0 0 1-.385.136.556.556 0 0 1-.387-.139.456.456 0 0 1-.151-.35c0-.14.05-.257.15-.35a.556.556 0 0 1 .388-.139z"/><path d="M3.499 9.425l.455-.862h1.013l-.825 1.524.865 1.605H3.988l-.492-.925-.483.925H1.988l.868-1.605-.822-1.524H3.06l.44.862z"/><path d="M6.31 11.692H5.33V7.25h.979v4.442z"/><path d="M8.553 10.813c0-.083-.043-.15-.13-.2-.087-.05-.252-.102-.495-.156a2.294 2.294 0 0 1-.602-.21 1.019 1.019 0 0 1-.362-.322.763.763 0 0 1-.124-.43.86.86 0 0 1 .356-.71c.237-.186.548-.28.932-.28.413 0 .745.094.996.28.25.188.376.434.376.738h-.978c0-.25-.133-.376-.397-.376a.39.39 0 0 0-.258.087.26.26 0 0 0-.104.211c0 .087.043.157.127.211.085.054.22.098.406.133.187.035.351.076.492.125.47.162.706.452.706.87 0 .285-.127.518-.382.697-.253.18-.58.269-.984.269-.268 0-.508-.048-.718-.145a1.2 1.2 0 0 1-.492-.393.89.89 0 0 1-.177-.523h.912c.004.144.052.25.145.318a.602.602 0 0 0 .356.098c.133 0 .232-.027.298-.08a.258.258 0 0 0 .101-.212z"/></svg>';
    static public $pencil = '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.24826 2.32734e-07C8.0492 -0.000156404 7.85823 0.0787584 7.7174 0.219374L1.3396 6.59434C1.24924 6.68749 1.18461 6.80249 1.15201 6.92808L0.0265198 11.0531C-0.082298 11.4527 0.153632 11.8649 0.553485 11.9737C0.682494 12.0088 0.818541 12.0088 0.94755 11.9737L5.07436 10.8487C5.20001 10.8161 5.31506 10.7515 5.40826 10.6612L11.7861 4.28623C11.9261 4.14316 12.0031 3.95009 11.9999 3.74998C11.9999 1.67892 10.3202 2.32734e-07 8.24826 2.32734e-07ZM5.68025 8.2537C5.30176 7.38738 4.61152 6.69476 3.74628 6.31309L8.54089 1.51874C9.55218 1.65114 10.348 2.44664 10.4805 3.45748L5.68025 8.2537Z"/></svg>';
    static public $trash = '<svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 0.5C10 0.223858 9.77614 0 9.5 0H4.5C4.22386 0 4 0.223858 4 0.5V2L0.5 2C0.223858 2 0 2.22386 0 2.5V3.5C0 3.77614 0.223857 4 0.5 4L13.5 4C13.7761 4 14 3.77614 14 3.5V2.5C14 2.22386 13.7761 2 13.5 2L10 2V0.5Z"></path><path d="M12.5 6C12.7761 6 13 6.22386 13 6.5V16C13 17.1046 12.1046 18 11 18H3C1.89543 18 1 17.1046 1 16L1 6.5C1 6.22386 1.22386 6 1.5 6L12.5 6Z"></path></svg>';
    static public $empty = '<svg><use xlink:href="img/sprite.svg#undefined"></use></svg>';
    static public $loading = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" width="30" height="30"><circle cx="50" cy="50" r="40" stroke-width="15" stroke="#007bff" stroke-dasharray="60 60" fill="none" stroke-linecap="round"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s"  keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform></circle><circle cx="50" cy="50" r="20" stroke-width="15" stroke="#28a745" stroke-dasharray="45 45" stroke-dashoffset="45" fill="none" stroke-linecap="round"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0 50 50;-360 50 50"></animateTransform></circle></svg>';

}
