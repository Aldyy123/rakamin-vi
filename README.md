# Article Post for Virtual Intership Rakamain
---

##### Apps using Laravel. This App providing API and CRUD GUI. Before running the application you may need migrating database and seed the factory.


### How to running is apps

Migration the database
``` shell
php artisan migrate --seed
```

Running the server
``` shell
php artisan serve
```

Running style csss tailwing
``` shell
npm run dev
```

if done running the all command is successful, next steep open the server in artisan serve.

---

### Rest API Path URL

All resource API use prefix v1
Example
```bash
/api/v1/categories
/api/v1/article
```

Don't Forget it this must be login and get token for authorization. You can login through url below.

```bash
/api/login
/api/register
```

---

### Detail whats data for post API


**Table Categories**
<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name row</th>
      <th scope="col">Description</th>
      <th scope="col">type</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td>name</td>
      <td>Name category</td>
      <td>string</td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>user_id</td>
      <td>Relation user Id</td>
      <td>int</td>
    </tr>
  </tbody>
</table>



**Table Articles**
<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name row</th>
      <th scope="col">Description</th>
      <th scope="col">type</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td>category_id</td>
      <td>Relation category</td>
      <td>int</td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>user_id</td>
      <td>Relation user Id</td>
      <td>int</td>
    </tr>
     <tr>
      <th scope="row">3</th>
      <td>title</td>
      <td>Title article</td>
      <td>string</td>
    </tr>
     <tr>
      <th scope="row">4</th>
      <td>content</td>
      <td>Content Article</td>
      <td>string</td>
    </tr>
     <tr>
      <th scope="row">5</th>
      <td>image</td>
      <td>Data image</td>
      <td>File</td>
    </tr>
  </tbody>
</table>


---

Thanks to visiting my repository