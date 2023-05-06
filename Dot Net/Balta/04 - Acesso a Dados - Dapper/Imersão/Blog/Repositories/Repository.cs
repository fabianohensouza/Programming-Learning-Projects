using System.Collections.Generic;
using Blog.Models;
using Dapper.Contrib.Extensions;
using Microsoft.Data.SqlClient;

namespace Blog.Repositories
{
    public class Repository<TModel> where TModel : class
    {

        private readonly SqlConnection _connection;

        public Repository(SqlConnection connection)
            => _connection = connection;

        public IEnumerable<TModel> Get()
            => _connection.GetAll<TModel>();

        public TModel Get(int id)
            => _connection.Get<TModel>(id);

        public void Create(TModel model)
            => _connection.Insert<TModel>(model);

        public void Update(TModel model)
            => _connection.Update<TModel>(model);

        public void Delete(TModel model)
            => _connection.Delete<TModel>(model);

        public void Delete(int id)
        {
            if (id != 0)
                return;

            var model = Get(id);
            _connection.Delete<TModel>(model);
        }
    }
}