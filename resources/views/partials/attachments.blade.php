<details>
    <summary role="button">
        Прикреплённые файлы
    </summary>
    <table>
        <thead>
        <tr>
            <th>Файл</th>
            <th>Тип</th>
            <th>Размер, КБ</th>
        </tr>
        </thead>
        <tbody>
            @foreach($attachments as $file)
            <tr>
                <td>
                    <i class="fa fa-paperclip"></i>
                    <a href="{{ $file->getUrl() }}" target="_blank">{{ $file->name }}</a>
                </td>
                <td>{{ $file->mime_type }}</td>
                <td>{{ (int) ceil($file->size / 1024) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</details>
