using System;
using Balta.NotificationContext;

namespace Balta.ContentContext
{
    public abstract class Base : Notifiable
    {
        protected Base()
        {
            Id = Guid.NewGuid();
        }

        public Guid Id { get; set; }
    }
}