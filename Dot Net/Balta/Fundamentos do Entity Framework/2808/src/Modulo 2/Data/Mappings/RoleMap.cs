using System.Collections.Generic;
using Blog.Models;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Blog.Data.Mappings
{
    public class RoleMap : IEntityTypeConfiguration<Role>
    {
        public void Configure(EntityTypeBuilder<Role> builder)
        {
            //Table
            builder.ToTable("Role");

            //primary Key
            builder.HasKey(x => x.Id);

            //Identity
            builder.Property(x => x.Id)
                .ValueGeneratedOnAdd()
                .UseIdentityColumn();

            //Properties
            builder.Property(x => x.Name)
                .IsRequired()
                .HasColumnName("Name")
                .HasColumnType("NVARCHAR")
                .HasMaxLength(80);

            builder.Property(x => x.Slug)
                .IsRequired()
                .HasColumnName("Slug")
                .HasColumnType("VARCHAR")
                .HasMaxLength(80);

            // Indexes
            builder.HasIndex(x => x.Slug, "IX_Category_Slug")
                .IsUnique();

            // Many to Many
            // builder.HasMany(x => x.Users)
            //     .WithMany(x => x.Roles)
            //     .UsingEntity<Dictionary<string, object>>(
            //         "UserRole",
            //         user => user.HasOne<Role>()
            //             .WithMany()
            //             .HasForeignKey("UserId")
            //             .HasConstraintName("FK_UserRole_RoleId")
            //             .OnDelete(DeleteBehavior.Cascade),

            //         user => user.HasOne<User>()
            //             .WithMany()
            //             .HasForeignKey("RoleId")
            //             .HasConstraintName("FK_UserRole_UserId")
            //             .OnDelete(DeleteBehavior.Cascade)
            //     );
        }
    }
}