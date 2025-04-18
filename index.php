<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userEmail = $_POST["email"];
    $productCategory = $_POST["category"];
    $adTitle = preg_replace("/[^a-zA-Zа-яА-Я0-9_\s]/u", "", $_POST["title"]); 
    $adContent = $_POST["textarea"];

    if (!empty($userEmail) && !empty($productCategory) && !empty($adTitle) && !empty($adContent))
    {
        $adFilePath = "$productCategory/" . str_replace("@","$",$userEmail) . "/" . str_replace(" ", "_", $adTitle) . ".txt";
        if (!file_exists($adFilePath))
        {
            mkdir("$productCategory/" . str_replace("@","$",$userEmail), 0777, true);
        }
        file_put_contents($adFilePath, $adContent);
    }
}

$allAds = [];
$availableCategories = ["Канцтовары", "Книги", "Учебники"];
foreach ($availableCategories as $category)
{
    $categoryDirectory = opendir($category);
    while ($userFolder = readdir($categoryDirectory))
    {
        if ($userFolder != "." && $userFolder != ".." && is_dir("$category/$userFolder"))
        {
            $adFiles = glob("$category/$userFolder/*.txt");
            $userEmail = str_replace("$","@",$userFolder);
            foreach ($adFiles as $file)
            {
                $fileContent = file_get_contents($file);
                $allAds[] = [
                    "category" => $category,
                    "title" => pathinfo($file, PATHINFO_FILENAME),
                    "content" => $fileContent,
                    "email" => $userEmail
                ];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Доска объявлений: Канцтовары, Книги, Учебники</title>
</head>
<body>
    <h2>Добавить объявление</h2>
    <form method="post">
        Категория:
        <select name="category" required>
            <option value="Канцтовары">Канцтовары</option>
            <option value="Книги">Книги</option>
            <option value="Учебники">Учебники</option>
        </select>
        <label for="title">Заголовок:</label>
        <input type="text" name="title" required>
        <label for="email">e-mail:</label>
        <input type="email" name="email" required>
        

        <label for="text">Текст объявления:</label>
        

        <textarea name="textarea" cols="100" rows="10" required>Опишите ваш товар...</textarea>
        

        <button>Добавить объявление</button>
    </form>
    <h3>Список объявлений:</h3>
    <table>
        <tr>
            <th>Категория</th>
            <th>Заголовок</th>
            <th>Контактный email</th>
            <th>Описание товара</th>
        </tr>
        <?php foreach ($allAds as $ad): ?>
        <tr>
            <td><?= htmlspecialchars($ad["category"]) ?></td>
            <td><?= htmlspecialchars($ad["title"]) ?></td>
            <td><?= htmlspecialchars($ad["email"]) ?></td>
            <td><pre><?= htmlspecialchars($ad["content"]) ?></pre></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>