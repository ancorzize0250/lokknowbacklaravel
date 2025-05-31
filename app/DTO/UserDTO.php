<?php

namespace App\DTO;

class UserDTO
{
    private string $identification;
    private string $name;
    private string $email;
    private int $id;
    private string $phone;
    

    /**
     * Get the value of identification
     */ 
    public function getIdentification()
    {
        return $this->identification;
    }

    /**
     * Set the value of identification
     *
     * @return  self
     */ 
    public function setIdentification($identification)
    {
        $this->identification = $identification;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function to_array()
    {
        return [
            'id' => $this->getId(),
            'identification' => $this->getIdentification(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
        ];
    }

    /**
     * Get the value of phone
     */ 
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */ 
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }
}