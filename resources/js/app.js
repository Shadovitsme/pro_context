import { isEmptyObject } from "jquery";
import "./bootstrap";

function emailValidate(email) {
    return String(email)
        .toLowerCase()
        .match(/[0-9a-z]+@[a-z]*.*/);
}
function dateCheck(date) {
    const today = new Date();
    let checkDate = new Date(date);
    today.setHours(12, 0, 0, 0);
    checkDate.setHours(12, 0, 0, 0);
    if (checkDate >= today) {
        return true;
    } else {
        return false;
    }
}

let results = [];

const result_queue = (header, body) => {
    results.unshift({
        color: Math.random() * 360,
        header: header,
        body: body,
    });

    if (results.length > 4) {
        results.pop();
    }

    $(".js--result-feed").html("");

    results.forEach((el) => {
        $("<div/>", {
            class: "card",
            style: `background: hsl(${el.color}, 50%, 16%)`,
        })
            .html(
                $("<div/>", { class: "card-body" }).append(
                    $("<h5/>", { calss: "card-header" }).html(el.header),
                    $("<p/>", { class: "card-text" }).html(el.body)
                )
            )
            .appendTo(".js--result-feed");
    });

    return true;
};

const api_pref = "/api/v1/";

$(".js--auth button").on("click", () => {
    let login = $(".js--auth .js--login").val().trim();
    let password = $(".js--auth .js--password").val().trim();
    if (login === "" || password === "") {
        alert("Не все поля заполнены!");
    } else {
        $.ajax({
            url: api_pref + `users/authenticate`,
            method: "POST",
            dataType: "json",
            contentType: "applicatio/json",
            data: JSON.stringify({
                login: login,
                password: password,
            }),
            success: function (data) {
                result_queue(
                    "Результат авторизации пользователя",
                    JSON.stringify(data)
                );
            },
        });
    }
});

$(".js--get button").on("click", () => {
    let id = $(".js--get .js--id").val().trim();
    let login = $(".js--get .js--login").val().trim();
    if (login === "" && id === "") {
        alert("Ни одно поле не заполнено!");
    } else {
        let payload = {};
        if (login !== "") {
            payload = {
                login: login,
            };
        }
        $.ajax({
            url: api_pref + `users/` + (id && login ? `${id}/` : id),
            method: "GET",
            dataType: "json",
            data: payload,
            success: function (data) {
                result_queue("Данные пользователя", JSON.stringify(data));
            },
        });
    }
});

$(".js--delete button").on("click", () => {
    let id = $(".js--delete .js--id").val().trim();
    if (id === "") {
        alert("Поле пустое!");
    } else {
        $.ajax({
            url: api_pref + `users/${id}`,
            method: "DELETE",
            dataType: "json",
            success: function (data) {
                result_queue(
                    "Результат удаления пользователя",
                    JSON.stringify(data)
                );
            },
        });
    }
});

$(".js--register button").on("click", () => {
    let login = $(".js--register .js--login").val().trim();
    let password = $(".js--register .js--password").val().trim();
    let birthDate = $(".js--register .js--birth-date").val();
    let email = $(".js--register .js--email").val().trim();
    if (login === "" || password === "" || email === "") {
        alert("Не все поля заполнены!");
    } else if (emailValidate(email) == null) {
        alert("некорректная почта!");
    } else if (dateCheck(birthDate)) {
        alert("Невалидная дата");
    } else {
        $.ajax({
            url: api_pref + `users`,
            method: "POST",
            dataType: "json",
            contentType: "applicatio/json",
            data: JSON.stringify({
                login: login,
                email: email,
                birth_date: birthDate,
                password: password,
            }),
            success: function (data) {
                result_queue(
                    "Результат регистрации пользователя",
                    JSON.stringify(data)
                );
            },
        });
    }
});

$(".js--update button").on("click", () => {
    let id = $(".js--update .js--id").val().trim();
    let login = $(".js--update .js--login").val().trim();
    let email = $(".js--update .js--email").val().trim();
    let birthDate = $(".js--update .js--birth-date").val();
    let password = $(".js--update .js--password").val().trim();
    if (id === "") {
        alert("id не предоствлен!");
    } else if (emailValidate(email) == null) {
        alert("Некорректная почта!");
    } else if (dateCheck(birthDate)) {
        alert("Невалидная дата");
    } else {
        alert(
            JSON.stringify({
                id: id,
                login: login,
                email: email,
                birth_date: birthDate,
                password: password,
            })
        );
        $.ajax({
            url: api_pref + `users/${id}`,
            method: "PATCH",
            dataType: "json",
            contentType: "applicatio/json",
            data: JSON.stringify({
                login: login,
                email: email,
                birth_date: birthDate,
                password: password,
            }),
            success: function (data) {
                result_queue(
                    "Результат изменения пользователя",
                    JSON.stringify(data)
                );
            },
        });
    }
});
